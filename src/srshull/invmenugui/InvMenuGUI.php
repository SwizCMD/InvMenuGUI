<?php

declare(strict_types=1);

namespace srshull\invmenugui;

use pocketmine\plugin\Plugin;

final class InvMenuGUI{
    /** @var \WeakMap<Plugin, GUIManager>|null */
    private static ?\WeakMap $managers = null;

    public static function init(Plugin $plugin) : GUIManager{
        self::$managers ??= new \WeakMap();
        if(isset(self::$managers[$plugin])){ return self::$managers[$plugin]; }
        $manager = new GUIManager($plugin);
        self::$managers[$plugin] = $manager;
        return $manager;
    }

    public static function create(int $size = 27) : Menu{
        return new Menu(self::getDefaultManager(), $size);
    }

    public static function createTyped(MenuType $type, string $title = "") : Menu{
        return new Menu(self::getDefaultManager(), $type, $title);
    }

    private static function getDefaultManager() : GUIManager{
        if(self::$managers === null || count(self::$managers) === 0){
            throw new \LogicException("Call InvMenuGUI::init(\$plugin) first");
        }
        $manager = null;
        foreach(self::$managers as $candidate){ $manager = $candidate; }
        if($manager === null){ throw new \LogicException("No InvMenuGUI manager initialized"); }
        return $manager;
    }
}
