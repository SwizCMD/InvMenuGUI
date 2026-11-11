<?php

declare(strict_types=1);

namespace srshull\\invmenugui\\transport;

use Closure;
use pocketmine\\block\\VanillaBlocks;
use pocketmine\\network\\mcpe\\protocol\\ContainerOpenPacket;
use pocketmine\\network\\mcpe\\protocol\\UpdateBlockPacket;
use pocketmine\\network\\mcpe\\protocol\\types\\BlockPosition;
use pocketmine\\network\\mcpe\\protocol\\types\\inventory\\WindowTypes;
use pocketmine\\player\\Player;
use pocketmine\\world\\Position;
use srshull\\invmenugui\\MenuType;
use srshull\\invmenugui\\VirtualInventory;

/**
 * Integrates with the player's existing PocketMine InventoryManager.
 * PocketMine remains the owner of window IDs and inventory synchronization.
 */
final class VirtualContainerTransport{

    /** @var array<string, array{Position, Closure}> */
    private array $active = [];

    public function open(Player $player, VirtualInventory $inventory, MenuType $type) : bool{
        $session = $player->getNetworkSession();
        $inventoryManager = $session->getInvManager();
        if($inventoryManager === null){
            return false;
        }

        $key = $player->getUniqueId()->toString();
        $this->close($player);

        $direction = $player->getDirectionVector();
        $base = $player->getPosition()->subtract($direction->x * 2, 0, $direction->z * 2)->floor();
        $position = new Position($base->x, $base->y, $base->z, $player->getWorld());

        $block = match($type){
            MenuType::HOPPER => VanillaBlocks::HOPPER(),
            MenuType::CHEST, MenuType::DOUBLE_CHEST => VanillaBlocks::CHEST()
        };

        $translator = $session->getTypeConverter()->getBlockTranslator();
        $session->sendDataPacket(UpdateBlockPacket::create(
            BlockPosition::fromVector3($position),
            $translator->internalIdToNetworkId($block->getStateId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        ));

        $windowType = $type === MenuType::HOPPER ? WindowTypes::HOPPER : WindowTypes::CONTAINER;
        $callback = static function(int $windowId, \\pocketmine\\inventory\\Inventory $candidate) use($inventory, $position, $windowType) : ?array{
            if($candidate !== $inventory){
                return null;
            }
            return [ContainerOpenPacket::blockInv($windowId, $windowType, BlockPosition::fromVector3($position))];
        };
        $inventoryManager->getContainerOpenCallbacks()->add($callback);
        $this->active[$key] = [$position, $callback];

        if(!$player->setCurrentWindow($inventory)){
            $this->close($player);
            return false;
        }
        return true;
    }

    /**
     * Removes the session callback and restores the original block only when
     * the viewer is still in the world where the fake block was sent.
     */
    public function close(Player $player) : void{
        $key = $player->getUniqueId()->toString();
        $active = $this->active[$key] ?? null;
        if($active === null){
            return;
        }

        [$position, $callback] = $active;
        $player->getNetworkSession()->getInvManager()?->getContainerOpenCallbacks()->remove($callback);

        // A world transfer changes the client's dimension. Sending an update
        // for the old world's coordinates into the new dimension is incorrect.
        if($player->getWorld() === $position->getWorld()){
            foreach($position->getWorld()->createBlockUpdatePackets([$position]) as $packet){
                $player->getNetworkSession()->sendDataPacket($packet);
            }
        }

        unset($this->active[$key]);
    }
}
