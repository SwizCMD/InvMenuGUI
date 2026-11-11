<?php

declare(strict_types=1);

namespace srshull\invmenugui;

use pocketmine\inventory\SimpleInventory;

final class GUIInventory extends SimpleInventory{
    public function __construct(int $size){
        if(!in_array($size, [9, 18, 27, 36, 45, 54], true)){
            throw new \InvalidArgumentException("GUI size must be 9, 18, 27, 36, 45 or 54");
        }
        parent::__construct($size);
    }
}
