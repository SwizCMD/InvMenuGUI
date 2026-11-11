<?php

declare(strict_types=1);

namespace srshull\invmenugui;

use Closure;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class Menu implements Listener{
    private GUIInventory $inventory;
    private ?Closure $clickListener = null;
    private ?Closure $openListener = null;
    private ?Closure $closeListener = null;
    private bool $allowItemMovement = false;
    private string $title;
    private MenuType $type;

    public function __construct(private GUIManager $manager, int|MenuType $layout = 27, string $title = ""){
        $this->type = $layout instanceof MenuType ? $layout : self::typeFromSize($layout);
        $size = $layout instanceof MenuType ? $layout->getSize() : $layout;
        $this->inventory = new GUIInventory($size);
        $this->title = $title;
        $manager->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $manager->getPlugin());
    }

    private static function typeFromSize(int $size) : MenuType{
        return match($size){
            5 => MenuType::HOPPER,
            54 => MenuType::DOUBLE_CHEST,
            default => MenuType::CHEST
        };
    }

    public function getType() : MenuType{ return $this->type; }
    /** Stores the desired title; current generic transport does not render it yet. */
    public function setName(string $title) : self{ $this->title = $title; return $this; }
    public function getName() : string{ return $this->title; }

    public function setItem(int $slot, Item $item) : self{ $this->inventory->setItem($slot, clone $item); return $this; }
    /** @param array<int, Item> $items */
    public function setContents(array $items) : self{ $this->inventory->setContents($items); return $this; }
    public function setClickListener(?Closure $listener) : self{ $this->clickListener = $listener; return $this; }
    public function setOpenListener(?Closure $listener) : self{ $this->openListener = $listener; return $this; }
    public function setCloseListener(?Closure $listener) : self{ $this->closeListener = $listener; return $this; }
    public function setAllowItemMovement(bool $allow) : self{ $this->allowItemMovement = $allow; return $this; }
    public function getInventory() : GUIInventory{ return $this->inventory; }
    public function getManager() : GUIManager{ return $this->manager; }

    public function open(Player $player) : bool{
        $ev = new InventoryOpenEvent($this->inventory, $player);
        $ev->call();
        if($ev->isCancelled()){ return false; }
        $player->setCurrentWindow($this->inventory);
        $this->manager->register($this, $player);
        if($this->openListener !== null){ ($this->openListener)($player, $this); }
        return true;
    }
    public function send(Player $player) : bool{ return $this->open($player); }

    public function close(Player $player) : bool{
        if($this->manager->getOpenMenu($player) !== $this){ return false; }
        $player->removeCurrentWindow();
        $this->manager->unregister($player, $this);
        return true;
    }

    #[\pocketmine\event\EventHandler]
    public function handleClose(InventoryCloseEvent $event) : void{
        if($event->getInventory() !== $this->inventory){ return; }
        $player = $event->getPlayer();
        $this->manager->unregister($player, $this);
        if($this->closeListener !== null){ ($this->closeListener)($player, $this); }
    }

    #[\pocketmine\event\EventHandler]
    public function handleTransaction(InventoryTransactionEvent $event) : void{
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();
        if($this->manager->getOpenMenu($player) !== $this){ return; }
        foreach($transaction->getActions() as $action){
            if(!$action instanceof SlotChangeAction || $action->getInventory() !== $this->inventory){ continue; }
            if($this->clickListener !== null){
                $result = ($this->clickListener)(new GUITransaction($player, $this, $action, $transaction));
                if($result === GUITransactionResult::CANCEL){ $event->cancel(); return; }
                if($result === GUITransactionResult::ALLOW){ continue; }
            }
            if(!$this->allowItemMovement){ $event->cancel(); return; }
        }
    }
}
