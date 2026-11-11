# Installation

## Virion
1. Add InvMenuGUI as a Virion build dependency.
2. Shade its classes into the consumer plugin PHAR.
3. Call `InvMenuGUI::init($this)` once during `onEnable()`.
4. Do not also install/use the standalone Phar for that same consumer.

## Standalone Phar
1. Place `InvMenuGUI.phar` in `plugins/`.
2. Add `depend: [InvMenuGUI]` to the consumer plugin's `plugin.yml`.
3. Retrieve the InvMenuGUI plugin instance from PluginManager.
4. Create menus through the service instance.

A consumer must choose exactly one integration mode.
