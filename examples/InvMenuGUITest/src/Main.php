<?php

declare(strict_types=1);

namespace srshull\invmenuguitest;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use srshull\invmenugui\GUITransaction;
use srshull\invmenugui\GUITransactionResult;
use srshull\invmenugui\InvMenuGUI;
use srshull\invmenugui\MenuType;

final class Main extends PluginBase{

    protected function onEnable() : void{
        InvMenuGUI::init($this);
        $this->getLogger()->info("InvMenuGUI test harness enabled. Use /invmenutest [chest|double|hopper].");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() !== "invmenutest"){
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage("Run this command in-game.");
            return true;
        }

        $type = match(strtolower($args[0] ?? "chest")){
            "chest" => MenuType::CHEST,
            "double", "double_chest" => MenuType::DOUBLE_CHEST,
            "hopper" => MenuType::HOPPER,
            default => null
        };
        if($type === null){
            $sender->sendMessage("Usage: /invmenutest [chest|double|hopper]");
            return true;
        }

        $menu = InvMenuGUI::createTyped($type, "InvMenuGUI Test");
        $menu->setItem(0, VanillaItems::DIAMOND())
            ->setItem(1, VanillaItems::EMERALD())
            ->setItem(2, VanillaItems::GOLD_INGOT())
            ->setItem($type->getSize() - 1, VanillaItems::PAPER());

        $menu->setOpenListener(function(Player $player) use($type) : void{
            $player->sendMessage("InvMenuGUI: opened " . $type->value . " (" . $type->getSize() . " slots).");
        });
        $menu->setClickListener(function(GUITransaction $transaction) : GUITransactionResult{
            $player = $transaction->getPlayer();
            $player->sendMessage("InvMenuGUI click: slot " . $transaction->getSlot() . " / " . $transaction->getClickedItem()->getName());
            return GUITransactionResult::CANCEL;
        });
        $menu->setCloseListener(function(Player $player) : void{
            $player->sendMessage("InvMenuGUI: menu closed and cleanup callback received.");
        });

        if(!$menu->open($sender)){
            $sender->sendMessage("InvMenuGUI: failed to open container. Check server logs and transport compatibility.");
            return true;
        }
        return true;
    }
}
