# API

## Factory / service
- `InvMenuGUI::init(Plugin $plugin): GUIManager`
- `InvMenuGUI::create(int $size = 27): Menu`
- `InvMenuGUIPlugin::createMenu(int $size = 27): Menu`

## Menu
- `setItem(int $slot, Item $item): self`
- `setContents(array $items): self`
- `setClickListener(?Closure $listener): self`
- `setOpenListener(?Closure $listener): self`
- `setCloseListener(?Closure $listener): self`
- `setAllowItemMovement(bool $allow): self`
- `open(Player $player): bool`
- `close(Player $player): bool`

## Click transaction
- `getPlayer(): Player`
- `getMenu(): Menu`
- `getSlot(): int`
- `getClickedItem(): Item`
- `getTargetItem(): Item`
- `getAction(): SlotChangeAction`
- `getTransaction(): InventoryTransaction`

Click callback returns `GUITransactionResult::CANCEL`, `GUITransactionResult::ALLOW`, or null to use the menu default. Validate callback behavior on the exact PMMP build you deploy.
