<?php

declare(strict_types=1);

namespace srshull\example;

use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use srshull\invmenugui\plugin\InvMenuGUIPlugin;
use srshull\invmenugui\GUITransaction;
use srshull\invmenugui\GUITransactionResult;

final class Main extends PluginBase implements Listener{

    private InvMenuGUIPlugin $gui;

    protected function onEnable() : void{
        $plugin = $this->getServer()->getPluginManager()->getPlugin("InvMenuGUI");
        if(!$plugin instanceof InvMenuGUIPlugin){
            throw new \RuntimeException("InvMenuGUI standalone plugin is missing");
        }
        $this->gui = $plugin;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function openExample(Player $player) : void{
        $menu = $this->gui->createMenu(27);
        $menu->setItem(13, VanillaItems::DIAMOND());
        $menu->setClickListener(function(GUITransaction $transaction) : GUITransactionResult{
            $transaction->getPlayer()->sendMessage("Clicked slot " . $transaction->getSlot());
            return GUITransactionResult::CANCEL;
        });
        $menu->open($player);
    }

    public function handleJoin(PlayerJoinEvent $event) : void{
        // Call openExample($event->getPlayer()) from your own command or interaction.
    }
}
