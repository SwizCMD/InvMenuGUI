<?php

declare(strict_types=1);

namespace srshull\\invmenugui;

/**
 * Logical container layouts supported by the public API.
 *
 * These values describe the intended Bedrock container shape. The current
 * transport still uses PocketMine's generic inventory window and does not
 * yet emulate fake blocks or protocol-specific container graphics.
 */
enum MenuType : string{
    case CHEST = "chest";
    case DOUBLE_CHEST = "double_chest";
    case HOPPER = "hopper";

    public function getSize() : int{
        return match($this){
            self::CHEST => 27,
            self::DOUBLE_CHEST => 54,
            self::HOPPER => 5
        };
    }
}
