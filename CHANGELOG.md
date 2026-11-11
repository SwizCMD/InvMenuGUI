# Changelog

## 0.2.0-dev.2 — integrated GUI test update
- Keeps a single in-game test command: /invmenutest.
- The test command now opens only the standard chest layout, removing ambiguous layout arguments.
- Improves virtual chest placement so the client receives the fake block in front of the player.
- Adds block-actor metadata for the virtual chest, including its display name and movable flag.
- Extends the command-to-open delay to reduce Bedrock chat/window handshake races.
- Adds clearer transport failure diagnostics.
- The standalone plugin distribution contains the library and its test command in one PHAR.

## 0.2.0-dev.1 — virtual transport foundation
- Adds logical CHEST, DOUBLE_CHEST and HOPPER menu layouts.
- Adds a per-viewer fake chest/hopper block update.
- Hooks virtual inventories into PocketMine's per-player InventoryManager container-open callback.
- Delegates window IDs, inventory content packets and slot synchronization to PocketMine.
- Restores the real world block state to the viewer when closing a menu.
- Adds transport architecture and test matrix documentation.
- DOUBLE_CHEST pairing and custom visible title rendering remain incomplete; this is not production-ready.

## 0.1.2
- Clarifies global PocketMine-MP scope and compatibility policy.
- Adds the official Tuff PMMP Discord community link.

## 0.1.1
- Establishes Virion and standalone Phar distributions and CI/release workflows.

## 0.1.0
- Initial prototype.
