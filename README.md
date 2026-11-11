# InvMenuGUI

PocketMine-MP inventory GUI library maintained by **SR.SHULL Dev**.

## 0.2.0-dev.1
The API now defines logical CHEST (27), DOUBLE_CHEST (54) and HOPPER (5) layouts, typed menu factories, title metadata, transaction callbacks and a send() alias.

**Important:** current transport remains PocketMine's generic inventory window. Fake-block graphics, visible custom titles, explicit container packets, double-chest pairing and protocol-specific synchronization are not implemented. This is not yet a drop-in replacement for Muqsit's InvMenu or production-certified.

## Example
```php
InvMenuGUI::init($this);
$menu = InvMenuGUI::createTyped(MenuType::HOPPER, "Crate options");
$menu->setClickListener(function(GUITransaction $tx) : GUITransactionResult {
    return GUITransactionResult::CANCEL;
});
$menu->send($player);
``

Requires PHP 8.1+ and PocketMine-MP API 5.x. See docs/API.md, docs/COMPATIBILITY.md and docs/TESTING.md.

[Tuff PMMP Discord](https://discord.gg/FzjNg252ay)

MIT License.
