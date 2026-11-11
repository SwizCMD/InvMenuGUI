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
use srshull\invmenugui\transport\VirtualContainerTransport;

final class Menu implements Listener{
    private VirtualInventory $inventory;
    private ?Closure $clickListener = null;
    private ?Closure $openListener = null;
    private ?Closure $closeListener = null;
    private bool $allowItemMovement = false;
    private string $title;
    private MenuType $type;
    private VirtualContainerTransport $transport;

    public function __construct(private GUIManager $manager, int|MenuType $layout = 27, string $title = ""){
        $this->type = $layout instanceof MenuType ? $layout : self::typeFromSize($layout);
        $size = $layout instanceof MenuType ? $layout->getSize() : $layout;
        $this->inventory = new VirtualInventory($size);
        $this->title = $title;
        $this->transport = new VirtualContainerTransport();
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
    /** Title is stored as API metadata; visible client rendering is not implemented yet. */
    public function setName(string $title) : self{ $this->title = $title; return $this; }
    public function getName() : string{ return $this->title; }

    public function setItem(int $slot, Item $item) : self{ $this->inventory->setItem($slot, clone $item); return $this; }
    /** @param array<int, Item> $items */
    public function setContents(array $items) : self{ $this->inventory->setContents($items); return $this; }
    public function setClickListener(?Closure $listener) : self{ $this->clickListener = $listener; return $this; }
    public function setOpenListener(?Closure $listener) : self{ $this->openListener = $listener; return $this; }
    public function setCloseListener(?Closure $listener) : self{ $this->closeListener = $listener; return $this; }
    public function setAllowItemMovement(bool $allow) : self{ $this->allowItemMovement = $allow; return $this; }
    public function getInventory() : VirtualInventory{ return $this->inventory; }
    public function getManager() : GUIManager{ return $this->manager; }

    public function open(Player $player) : bool{
        // Close any previously tracked InvMenuGUI menu first. This ensures its
        // real-block restoration cannot overwrite the new menu's fake graphic.
        $previous = $this->manager->getOpenMenu($player);
        if($previous !== null && $previous !== $this){
            $previous->close($player);
        }
        $ev = new InventoryOpenEvent($this->inventory, $player);
        $ev->call();
        if($ev->isCancelled()){ return false; }
        if(!$this->transport->open($player, $this->inventory, $this->type)){
            return false;
        }
        $this->manager->register($this, $player);
        if($this->openListener !== null){ ($this->openListener)($player, $this); }
        return true;
    }

    public function send(Player $player) : bool{ return $this->open($player); }

    public function close(Player $player) : bool{
        if($this->manager->getOpenMenu($player) !== $this){ return false; }
        $player->removeCurrentWindow();
        $this->transport->close($player);
        $this->manager->unregister($player, $this);
        return true;
    }

    #[\pocketmine\event\EventHandler]
    public function handleClose(InventoryCloseEvent $event) : void{
        if($event->getInventory() !== $this->inventory){ return; }
        $player = $event->getPlayer();
        $this->transport->close($player);
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
