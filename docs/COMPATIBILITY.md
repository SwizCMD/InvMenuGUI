# Compatibility policy

InvMenuGUI is a **PocketMine-MP-focused, global GUI library**. It is designed around PocketMine-MP APIs rather than being maintained for a separate server fork.

## Current baseline
- PocketMine-MP API 5.x
- PHP 8.1+

## Keeping up with PocketMine-MP
When PocketMine-MP publishes API updates, InvMenuGUI will review the API changes, update affected code, run syntax/build checks and perform live-server tests before declaring the new version supported.

Compatibility is not automatic: a new PMMP API release may introduce breaking changes. Each supported PMMP version must be listed and validated explicitly.

## Validation requirements
Before claiming support for a PMMP build, test:
- Opening and closing menus
- Inventory click and transaction cancellation
- Item movement and storage behavior
- Repeated opens and closes
- Multiple simultaneous players
- Disconnect cleanup
- Server shutdown and reload behavior

Record the exact PocketMine-MP version, API version, protocol and PHP version with each compatibility report. Do not claim a version is supported based on syntax checks alone.
