<?php

declare(strict_types=1);

namespace srshull\invmenugui;

use pocketmine\inventory\SimpleInventory;

/**
 * Inventory marker recognized by InvMenuGUI's per-player container-open
 * callback. PocketMine still owns window IDs and item-stack synchronization.
 */
final class VirtualInventory extends SimpleInventory{
    public function __construct(int $size){
        parent::__construct($size);
    }
}
