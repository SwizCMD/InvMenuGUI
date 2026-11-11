<?php

declare(strict_types=1);

namespace srshull\invmenugui;

use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

final class GUIManager implements Listener{

    /** @var array<string, Menu> */
    private array $openMenus = [];

    public function __construct(private Plugin $plugin){}

    public function getPlugin() : Plugin{ return $this->plugin; }

    public function register(Menu $menu, Player $player) : void{
        $this->openMenus[$player->getUniqueId()->toString()] = $menu;
    }

    public function unregister(Player $player, ?Menu $expected = null) : void{
        $key = $player->getUniqueId()->toString();
        if(!isset($this->openMenus[$key])){ return; }
        if($expected !== null && $this->openMenus[$key] !== $expected){ return; }
        unset($this->openMenus[$key]);
    }

    public function getOpenMenu(Player $player) : ?Menu{
        return $this->openMenus[$player->getUniqueId()->toString()] ?? null;
    }
}
