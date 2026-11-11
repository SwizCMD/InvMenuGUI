# API

## Factories
- `InvMenuGUI::init(Plugin $plugin): GUIManager`
- `InvMenuGUI::create(int $size = 27): Menu`
- `InvMenuGUI::createTyped(MenuType $type, string $title = ""): Menu`
- Standalone service: `createMenu(int $size = 27)` and `createTypedMenu(MenuType $type, string $title = "")`

## Types
- `MenuType::CHEST` — 27 logical slots, fake chest graphic.
- `MenuType::DOUBLE_CHEST` — 54 logical slots; paired graphics not yet implemented.
- `MenuType::HOPPER` — 5 logical slots, fake hopper graphic.

## Menu API
- `setName(string $title): self`, `getName(): string`, `getType(): MenuType`
- `setItem(int $slot, Item $item): self`, `setContents(array $items): self`
- `getInventory(): VirtualInventory`
- `setClickListener(?Closure $listener): self`
- `setOpenListener(?Closure $listener): self`, `setCloseListener(?Closure $listener): self`
- `setAllowItemMovement(bool $allow): self`
- `open(Player $player): bool`, `send(Player $player): bool`, `close(Player $player): bool`

## Transport
The transport registers a per-player callback through PocketMine's InventoryManager. PocketMine allocates the container ID and synchronizes item contents and slot changes. InvMenuGUI sends the fake block state to the viewer and restores the actual world block update on close. See [TRANSPORT.md](TRANSPORT.md).

## Transaction
Callbacks receive `GUITransaction`, including player, menu, slot, source/target items, action and transaction. Return `GUITransactionResult::CANCEL` or `ALLOW`; null applies the menu default.

## Limitations
Paired DOUBLE_CHEST graphics/protocol pairing, visible custom title rendering, and tested disconnect/world-change cleanup are not complete. Do not treat this build as a drop-in replacement for Muqsit's InvMenu or production-ready.
