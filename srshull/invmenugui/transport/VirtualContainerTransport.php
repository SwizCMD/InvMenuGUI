<?php
declare(strict_types=1);
namespace srshull\invmenugui\transport;
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
final class VirtualContainerTransport{
    /** @var array<string, array{list<Position>, \Closure}> */ private array $active = [];
    public function open(Player $player, VirtualInventory $inventory, MenuType $type, string $title = "") : bool{
        $session = $player->getNetworkSession();
        $inventoryManager = $session->getInvManager();
        if($inventoryManager === null){ return false; }
        $key = $player->getUniqueId()->toString();
        $this->close($player);
        $direction = $player->getDirectionVector();
        $vector = $player->getPosition()->add($direction->x * 2, 0, $direction->z * 2)->floor();
        $primary = new Position($vector->x, $vector->y, $vector->z, $player->getWorld());
        $positions = [$primary];
        if($type === MenuType::DOUBLE_CHEST){
            $facing = self::getChestFacing($direction->x, $direction->z);
            $side = self::getPairSideOffset($facing);
            $positions[] = new Position($primary->x + $side[0], $primary->y, $primary->z + $side[1], $primary->getWorld());
            $this->sendDoubleChest($player, $positions, $facing);
        }else{
            $block = match($type){MenuType::HOPPER => VanillaBlocks::HOPPER(), MenuType::CHEST => VanillaBlocks::CHEST(), MenuType::DOUBLE_CHEST => throw new \LogicException("Double chest handled above")};
            self::sendBlock($player, $primary, $block->getStateId());
            self::sendBlockActorData($player, $primary, $type === MenuType::HOPPER ? "Hopper" : "Chest", $title);
        }
        $windowType = $type === MenuType::HOPPER ? WindowTypes::HOPPER : WindowTypes::CONTAINER;
        $callback = static function(int $windowId, \pocketmine\inventory\Inventory $candidate) use($inventory, $primary, $windowType) : ?array{
            if($candidate !== $inventory){ return null; }
            return [ContainerOpenPacket::blockInv($windowId, $windowType, BlockPosition::fromVector3($primary))];
        };
        $inventoryManager->getContainerOpenCallbacks()->add($callback);
        $this->active[$key] = [$positions, $callback];
        if(!$player->setCurrentWindow($inventory)){ $this->close($player); return false; }
        return true;
    }
    private static function getChestFacing(float $x, float $z) : int{
        if(abs($x) > abs($z)){ return $x > 0 ? Facing::WEST : Facing::EAST; }
        return $z > 0 ? Facing::NORTH : Facing::SOUTH;
    }
    /** @return array{int, int} */
    private static function getPairSideOffset(int $facing) : array{
        return match($facing){Facing::NORTH, Facing::SOUTH => [1, 0], Facing::EAST, Facing::WEST => [0, 1], default => throw new \InvalidArgumentException("Chest facing must be horizontal")};
    }
    /** @param list<Position> $positions */
    private function sendDoubleChest(Player $player, array $positions, int $facing) : void{
        [$first, $second] = $positions;
        $state = VanillaBlocks::CHEST()->setFacing($facing)->getStateId();
        self::sendBlock($player, $first, $state);
        self::sendBlock($player, $second, $state);
        self::sendChestPairData($player, $first, $second);
        self::sendChestPairData($player, $second, $first);
    }
    private static function sendBlock(Player $player, Position $position, int $stateId) : void{
        $translator = $player->getNetworkSession()->getTypeConverter()->getBlockTranslator();
        $player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(BlockPosition::fromVector3($position), $translator->internalIdToNetworkId($stateId), UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL));
    }
    private static function sendBlockActorData(Player $player, Position $position, string $identifier, string $title = "") : void{
        $nbt = CompoundTag::create()->setString("id", $identifier)->setInt("x", $position->getFloorX())->setInt("y", $position->getFloorY())->setInt("z", $position->getFloorZ())->setByte("isMovable", 1);
        if($title !== ""){ $nbt->setString("CustomName", $title); }
        $player->getNetworkSession()->sendDataPacket(BlockActorDataPacket::create(BlockPosition::fromVector3($position), new CacheableNbt($nbt)));
    }
    private static function sendChestPairData(Player $player, Position $position, Position $pair) : void{
        $nbt = CompoundTag::create()->setString("id", "Chest")->setInt("x", $position->getFloorX())->setInt("y", $position->getFloorY())->setInt("z", $position->getFloorZ())->setInt("pairx", $pair->getFloorX())->setInt("pairz", $pair->getFloorZ())->setByte("isMovable", 1);
        $player->getNetworkSession()->sendDataPacket(BlockActorDataPacket::create(BlockPosition::fromVector3($position), new CacheableNbt($nbt)));
    }
    public function close(Player $player) : void{
        $key = $player->getUniqueId()->toString();
        $active = $this->active[$key] ?? null;
        if($active === null){ return; }
        [$positions, $callback] = $active;
        $player->getNetworkSession()->getInvManager()?->getContainerOpenCallbacks()->remove($callback);
        if($positions[0]->getWorld() === $player->getWorld()){
            foreach($positions[0]->getWorld()->createBlockUpdatePackets($positions) as $packet){ $player->getNetworkSession()->sendDataPacket($packet); }
        }
        unset($this->active[$key]);
    }
}
