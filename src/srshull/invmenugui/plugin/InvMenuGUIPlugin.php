<?php

declare(strict_types=1);

namespace srshull\invmenugui\plugin;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use srshull\invmenugui\GUITransaction;
use srshull\invmenugui\GUITransactionResult;
use srshull\invmenugui\GUIManager;
use srshull\invmenugui\InvMenuGUI;
use srshull\invmenugui\Menu;
use srshull\invmenugui\MenuType;

final class InvMenuGUIPlugin extends PluginBase{
    private GUIManager $guiManager;
    protected function onEnable() : void{
        $this->guiManager = InvMenuGUI::init($this);
        $this->getLogger()->info("InvMenuGUI 0.2.0-dev.1 loaded with bundled library.");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() !== "invmenutest"){
            return false;
        }
        if(!$sender instanceof Player){
            $this->getLogger()->warning("The invmenutest command can only be used in-game.");
            return true;
        }

        $type = match(strtolower($args[0] ?? "chest")){
            "chest" => MenuType::CHEST,
            "double", "double_chest" => MenuType::DOUBLE_CHEST,
            "hopper" => MenuType::HOPPER,
            default => null
        };
        if($type === null){
            $this->getLogger()->warning("Invalid InvMenuGUI test type. Use chest, double or hopper.");
            return true;
        }

        try{
            $menu = new Menu($this->guiManager, $type, "InvMenuGUI Test");
            $menu->setItem(0, VanillaItems::DIAMOND())
                ->setItem(1, VanillaItems::EMERALD())
                ->setItem(2, VanillaItems::GOLD_INGOT())
                ->setItem($type->getSize() - 1, VanillaItems::PAPER());
            $menu->setClickListener(static function(GUITransaction $transaction) : GUITransactionResult{
                return GUITransactionResult::CANCEL;
            });
            if(!$menu->open($sender)){
                $this->getLogger()->warning("InvMenuGUI could not schedule the requested container.");
            }
        }catch(\\Throwable $e){
            $this->getLogger()->error("InvMenuGUI test failed: " . $e->getMessage());
            $this->getLogger()->logException($e);
        }
        return true;
    }
    public function getGUIManager() : GUIManager{ return $this->guiManager; }
    public function createMenu(int $size = 27) : Menu{ return new Menu($this->guiManager, $size); }
    public function createTypedMenu(MenuType $type, string $title = "") : Menu{
        return new Menu($this->guiManager, $type, $title);
    }
}
