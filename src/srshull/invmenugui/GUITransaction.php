<?php

declare(strict_types=1);

namespace srshull\invmenugui;

use pocketmine\inventory\InventoryTransaction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class GUITransaction{

    public function __construct(
        private Player $player,
        private Menu $menu,
        private SlotChangeAction $action,
        private InventoryTransaction $transaction
    ){}

    public function getPlayer() : Player{ return $this->player; }
    public function getMenu() : Menu{ return $this->menu; }
    public function getSlot() : int{ return $this->action->getSlot(); }
    public function getClickedItem() : Item{ return $this->action->getSourceItem(); }
    public function getTargetItem() : Item{ return $this->action->getTargetItem(); }
    public function getAction() : SlotChangeAction{ return $this->action; }
    public function getTransaction() : InventoryTransaction{ return $this->transaction; }
}
