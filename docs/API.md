# API

## Factories
- `InvMenuGUI::init(Plugin $plugin): GUIManager`
- `InvMenuGUI::create(int $size = 27): Menu`
- `InvMenuGUI::createTyped(MenuType $type, string $title = ""): Menu`
- Standalone service: `createMenu(int $size = 27)` and `createTypedMenu(MenuType $type, string $title = "")`

## Types
- `MenuType::CHEST` — 27 logical slots, one virtual chest block.
- `MenuType::DOUBLE_CHEST` — 54 logical slots, two virtual chest blocks with reciprocal pair coordinates.
- `MenuType::HOPPER` — 5 logical slots, one virtual hopper block.

## Menu API
- `setName(string $title): self`, `getName(): string`, `getType(): MenuType`
- `setItem(int $slot, Item $item): self`, `setContents(array $items): self`
- `getInventory(): VirtualInventory`
- `setClickListener(?Closure $listener): self`
- `setOpenListener(?Closure $listener): self`, `setCloseListener(?Closure $listener): self`
- `setAllowItemMovement(bool $allow): self`
- `open(Player $player): bool`, `send(Player $player): bool`, `close(Player $player): bool`

## Transport
The transport registers a per-player callback through PocketMine's InventoryManager. PocketMine allocates the container ID and synchronizes item contents and slot changes. InvMenuGUI sends virtual block state(s) to the viewer and restores the actual world block update(s) on close. See [TRANSPORT.md](TRANSPORT.md).

## Transaction
Callbacks receive `GUITransaction`, including player, menu, slot, source/target items, action and transaction. Return `GUITransactionResult::CANCEL` or `ALLOW`; null applies the menu default.

## Limitations
Double Chest pairing, close behavior, chunk-edge placement, simultaneous viewers, disconnect/world-change cleanup and client transaction behavior still require runtime validation. Custom visible title rendering is not implemented. Do not treat this development build as a drop-in replacement for Muqsit's InvMenu or production-ready.
