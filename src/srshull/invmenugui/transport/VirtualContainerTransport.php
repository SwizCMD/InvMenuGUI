<?php

declare(strict_types=1);

namespace srshull\invmenugui\transport;

use Closure;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;
use srshull\invmenugui\MenuType;
use srshull\invmenugui\VirtualInventory;

/**
 * Per-viewer virtual container transport.
 *
 * PocketMine owns window IDs and inventory synchronization. This class only
 * supplies the fake block(s), block-actor pairing data and the open callback.
 */
final class VirtualContainerTransport{

    /**
     * @var array<string, array{list<Position>, Closure}>
     */
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
        $primary = new Position($base->x, $base->y, $base->z, $player->getWorld());
        $positions = [$primary];

        if($type === MenuType::DOUBLE_CHEST){
            $facing = self::getChestFacing($direction->x, $direction->z);
            $side = self::getPairSideOffset($facing);
            $positions[] = new Position($primary->x + $side[0], $primary->y, $primary->z + $side[1], $primary->getWorld());
            $this->sendDoubleChest($player, $positions, $facing);
        }else{
            $block = match($type){
                MenuType::HOPPER => VanillaBlocks::HOPPER(),
                MenuType::CHEST => VanillaBlocks::CHEST(),
                MenuType::DOUBLE_CHEST => throw new \LogicException("Double chest handled above")
            };
            self::sendBlock($player, $primary, $block->getStateId());
        }

        $windowType = $type === MenuType::HOPPER ? WindowTypes::HOPPER : WindowTypes::CONTAINER;
        $callback = static function(int $windowId, \pocketmine\inventory\Inventory $candidate) use($inventory, $primary, $windowType) : ?array{
            if($candidate !== $inventory){
                return null;
            }
            return [ContainerOpenPacket::blockInv($windowId, $windowType, BlockPosition::fromVector3($primary))];
        };
        $inventoryManager->getContainerOpenCallbacks()->add($callback);
        $this->active[$key] = [$positions, $callback];

        if(!$player->setCurrentWindow($inventory)){
            $this->close($player);
            return false;
        }
        return true;
    }

    /**
     * Chooses the horizontal chest facing opposite to the viewer's look vector.
     */
    private static function getChestFacing(float $directionX, float $directionZ) : int{
        if(abs($directionX) > abs($directionZ)){
            return $directionX > 0 ? Facing::WEST : Facing::EAST;
        }
        return $directionZ > 0 ? Facing::NORTH : Facing::SOUTH;
    }

    /**
     * A double chest is paired along the axis perpendicular to its facing.
     *
     * @return array{int, int}
     */
    private static function getPairSideOffset(int $facing) : array{
        return match($facing){
            Facing::NORTH, Facing::SOUTH => [1, 0],
            Facing::EAST, Facing::WEST => [0, 1],
            default => throw new \InvalidArgumentException("Chest facing must be horizontal")
        };
    }

    /**
     * Sends both chest block states, then reciprocal block-actor pair data.
     *
     * PMMP's native Chest block state only represents facing. Bedrock pairing
     * metadata is supplied through the same pairx/pairz tile fields used by
     * PMMP's real Chest tile implementation.
     *
     * @param list<Position> $positions
     */
    private function sendDoubleChest(Player $player, array $positions, int $facing) : void{
        [$first, $second] = $positions;
        $stateId = VanillaBlocks::CHEST()->setFacing($facing)->getStateId();

        self::sendBlock($player, $first, $stateId);
        self::sendBlock($player, $second, $stateId);

        self::sendChestPairData($player, $first, $second);
        self::sendChestPairData($player, $second, $first);
    }

    private static function sendBlock(Player $player, Position $position, int $stateId) : void{
        $translator = $player->getNetworkSession()->getTypeConverter()->getBlockTranslator();
        $player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
            BlockPosition::fromVector3($position),
            $translator->internalIdToNetworkId($stateId),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        ));
    }

    private static function sendChestPairData(Player $player, Position $position, Position $pair) : void{
        $nbt = CompoundTag::create()
            ->setString("id", "Chest")
            ->setInt("x", $position->getFloorX())
            ->setInt("y", $position->getFloorY())
            ->setInt("z", $position->getFloorZ())
            ->setInt("pairx", $pair->getFloorX())
            ->setInt("pairz", $pair->getFloorZ());

        $player->getNetworkSession()->sendDataPacket(BlockActorDataPacket::create(
            BlockPosition::fromVector3($position),
            new CacheableNbt($nbt)
        ));
    }

    /**
     * Removes the callback and restores every virtual block in its source world.
     */
    public function close(Player $player) : void{
        $key = $player->getUniqueId()->toString();
        $active = $this->active[$key] ?? null;
        if($active === null){
            return;
        }

        [$positions, $callback] = $active;
        $player->getNetworkSession()->getInvManager()?->getContainerOpenCallbacks()->remove($callback);

        // The old dimension is no longer visible after a world transfer.
        if($positions[0]->getWorld() === $player->getWorld()){
            foreach($positions[0]->getWorld()->createBlockUpdatePackets($positions) as $packet){
                $player->getNetworkSession()->sendDataPacket($packet);
            }
        }

        unset($this->active[$key]);
    }
}
