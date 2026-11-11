# API

## Factories
- `InvMenuGUI::init(Plugin $plugin): GUIManager`
- `InvMenuGUI::create(int $size = 27): Menu` (backwards compatible)
- `InvMenuGUI::createTyped(MenuType $type, string $title = ""): Menu`
- Standalone service: `createMenu(int $size = 27)` and `createTypedMenu(MenuType $type, string $title = "")`

## Types
- `MenuType::CHEST` — 27 logical slots
- `MenuType::DOUBLE_CHEST` — 54 logical slots
- `MenuType::HOPPER` — 5 logical slots

## Menu API
- `setName(string $title): self`, `getName(): string`, `getType(): MenuType`
- `setItem(int $slot, Item $item): self`, `setContents(array $items): self`
- `getInventory(): GUIInventory`
- `setClickListener(?Closure $listener): self`
- `setOpenListener(?Closure $listener): self`, `setCloseListener(?Closure $listener): self`
- `setAllowItemMovement(bool $allow): self`
- `open(Player $player): bool`, `send(Player $player): bool`, `close(Player $player): bool`

## Transaction
Callbacks receive `GUITransaction`, including player, menu, slot, source/target items, action and transaction. Return `GUITransactionResult::CANCEL` or `ALLOW`; null uses the menu default.

## Known transport gap
0.2.0-dev.1 exposes logical layout types only. It still opens a generic PocketMine inventory. Fake chest/hopper blocks, visible custom titles, raw container open/close packets, double-chest pairing and protocol-specific synchronization remain unimplemented. This is not a drop-in replacement for Muqsit's InvMenu.
