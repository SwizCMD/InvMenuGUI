# InvMenuGUI

PocketMine-MP inventory GUI library maintained by **SR.SHULL Dev**.

## 0.2.0-dev.1 transport development
- Typed logical layouts: CHEST (27), DOUBLE_CHEST (54), HOPPER (5).
- Per-viewer fake block state for chest and hopper.
- Per-player PocketMine InventoryManager container-open callback integration.
- PocketMine remains responsible for window IDs, content serialization and item-stack synchronization.
- Fake block restoration is sent to the viewer when the menu closes.
- Transaction, open and close callbacks.

**Important limitations:** DOUBLE_CHEST currently has a 54-slot logical inventory but only a single chest graphic; proper paired chest states and protocol-dependent pairing are not complete. Custom visible titles are not rendered yet. Disconnect/world-change cleanup, chunk-edge behavior, close acknowledgements, simultaneous viewers and client transaction behavior still require runtime testing. This is a development preview, not production-certified and not yet a complete drop-in replacement for Muqsit's InvMenu.

## Example
```php
InvMenuGUI::init($this);
$menu = InvMenuGUI::createTyped(MenuType::HOPPER, "Crate options");
$menu->setClickListener(function(GUITransaction $tx) : GUITransactionResult {
    return GUITransactionResult::CANCEL;
});
$menu->send($player);
``

Requires PHP 8.1+ and PocketMine-MP API 5.x. See docs/API.md, docs/TRANSPORT.md and docs/TESTING.md.

[Tuff PMMP Discord](https://discord.gg/FzjNg252ay)

MIT License.
