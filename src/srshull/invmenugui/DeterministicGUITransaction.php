<?php

declare(strict_types=1);

namespace srshull\\invmenugui;

use pocketmine\\item\\Item;
use pocketmine\\player\\Player;

/** Read-only snapshot for menu callbacks. */
final class DeterministicGUITransaction{
    public function __construct(private GUITransaction $transaction){}
    public function getPlayer() : Player{ return $this->transaction->getPlayer(); }
    public function getSlot() : int{ return $this->transaction->getSlot(); }
    public function getItemClicked() : Item{ return $this->transaction->getClickedItem(); }
    public function getItemClickedWith() : Item{ return $this->transaction->getTargetItem(); }
}
