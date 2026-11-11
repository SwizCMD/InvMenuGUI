<?php

declare(strict_types=1);

namespace srshull\invmenugui;

/**
 * Logical container layouts supported by the public API.
 *
 * These values describe the intended Bedrock container shape. CHEST and
 * HOPPER use one virtual block; DOUBLE_CHEST sends two paired chest blocks.
 * The client-side appearance still requires runtime validation per protocol.
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
