# InvMenuGUI

**Advanced inventory GUI library for PocketMine-MP 5**  
Maintained by **SR.SHULL Dev**.

InvMenuGUI is a PHP library for creating virtual inventory menus on Minecraft Bedrock servers running PocketMine-MP 5. It is distributed in two integration modes:

- **Virion:** shaded into each consumer plugin.
- **Standalone Phar:** installed once as a shared plugin service.

> **Important:** A consumer plugin must use exactly one distribution mode. Never shade the Virion into a plugin that also depends on the standalone Phar.

## Requirements
- PHP 8.1+
- PocketMine-MP API 5.x
- Fork compatibility must be verified separately on a live server.

## Virion usage

Initialize once in your plugin's `onEnable()`:

```php
use srshull\invmenugui\InvMenuGUI;

protected function onEnable() : void{
    InvMenuGUI::init($this);
}
```

Create a button menu:

```php
use pocketmine\item\VanillaItems;
use srshull\invmenugui\InvMenuGUI;
use srshull\invmenugui\GUITransaction;
use srshull\invmenugui\GUITransactionResult;

$menu = InvMenuGUI::create(27);
$menu->setItem(13, VanillaItems::DIAMOND());
$menu->setClickListener(function(GUITransaction $tx) : GUITransactionResult{
    $tx->getPlayer()->sendMessage("You clicked slot " . $tx->getSlot());
    return GUITransactionResult::CANCEL;
});
$menu->open($player);
```

## Standalone Phar usage

Install `InvMenuGUI.phar` in `plugins/` and declare a hard dependency in your consumer's `plugin.yml`:

```yaml
depend:
  - InvMenuGUI
```

Retrieve the service plugin and call `createMenu(27)` on its `InvMenuGUIPlugin` instance. See [docs/INSTALLATION.md](docs/INSTALLATION.md).

## Features in this development baseline
- 9, 18, 27, 36, 45 and 54 slot menus
- Open, click and close callbacks
- Per-player active menu tracking
- Button-menu default that cancels item movement
- Optional item movement mode
- Virion metadata and standalone plugin entry point
- GitHub Actions syntax/build validation and release workflow

## Planned / not yet implemented
- Fake chest block graphics and client-side custom title rendering
- Pagination
- Persistence/database adapters
- Full automated in-server integration test suite

## Build
```bash
composer validate --no-check-publish
find src examples -type f -name '*.php' -print0 | xargs -0 -n1 php -l
php -d phar.readonly=0 tools/build.php
```

## Validation status
This is a **development baseline**, not a production-certified release. PHP syntax and packaging checks do not prove runtime compatibility. Test on your exact PMMP/Altay build using [docs/TESTING.md](docs/TESTING.md) before production deployment. Compatibility notes are in [docs/COMPATIBILITY.md](docs/COMPATIBILITY.md).

## Documentation
- [Installation](docs/INSTALLATION.md)
- [API reference](docs/API.md)
- [Virion vs standalone integration](docs/INTEGRATION.md)
- [Compatibility](docs/COMPATIBILITY.md)
- [Build instructions](docs/BUILD.md)
- [Manual test checklist](docs/TESTING.md)
- [Release process](docs/RELEASES.md)
- [Changelog](CHANGELOG.md)

## License
MIT — see [LICENSE](LICENSE).
