<?php

declare(strict_types=1);

namespace srshull\invmenugui\plugin;

use pocketmine\plugin\PluginBase;
use srshull\invmenugui\GUIManager;
use srshull\invmenugui\InvMenuGUI;
use srshull\invmenugui\Menu;
use srshull\invmenugui\MenuType;

final class InvMenuGUIPlugin extends PluginBase{
    private GUIManager $guiManager;
    protected function onEnable() : void{
        $this->guiManager = InvMenuGUI::init($this);
        $this->getLogger()->info("InvMenuGUI 0.2.0-dev.1 initialized (generic transport mode).");
    }
    public function getGUIManager() : GUIManager{ return $this->guiManager; }
    public function createMenu(int $size = 27) : Menu{ return new Menu($this->guiManager, $size); }
    public function createTypedMenu(MenuType $type, string $title = "") : Menu{
        return new Menu($this->guiManager, $type, $title);
    }
}
