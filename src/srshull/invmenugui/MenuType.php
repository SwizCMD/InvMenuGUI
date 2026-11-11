<?php
declare(strict_types=1);
namespace srshull\\invmenugui;

/** Supported Bedrock container layouts. */
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
