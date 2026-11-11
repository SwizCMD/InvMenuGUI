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
        $this->getLogger()->info("InvMenuGUI 0.2.0-dev.2 loaded; use /invmenutest in-game.");
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() !== "invmenutest"){ return false; }
        if(!$sender instanceof Player){ $sender->sendMessage("Run this command in-game."); return true; }
        try{
            $menu = new Menu($this->guiManager, MenuType::CHEST, "InvMenuGUI Test");
            $menu->setItem(10, VanillaItems::DIAMOND())->setItem(13, VanillaItems::EMERALD())->setItem(16, VanillaItems::GOLD_INGOT())->setItem(22, VanillaItems::PAPER());
            $menu->setClickListener(static function(GUITransaction $transaction) : GUITransactionResult{ return GUITransactionResult::CANCEL; });
            if(!$menu->open($sender)){ $sender->sendMessage("InvMenuGUI: could not start the virtual chest."); $this->getLogger()->warning("The virtual chest open request was rejected."); }
        }catch(\Throwable $e){
            $sender->sendMessage("InvMenuGUI: an error occurred; check the server console.");
            $this->getLogger()->error("InvMenuGUI test failed: " . $e->getMessage());
            $this->getLogger()->logException($e);
        }
        return true;
    }
    public function getGUIManager() : GUIManager{ return $this->guiManager; }
    public function createMenu(int $size = 27) : Menu{ return new Menu($this->guiManager, $size); }
    public function createTypedMenu(MenuType $type, string $title = "") : Menu{ return new Menu($this->guiManager, $type, $title); }
}
