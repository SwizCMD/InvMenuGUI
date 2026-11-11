# Project status

**Version:** 0.1.2 — development baseline.

InvMenuGUI is a global GUI library focused on PocketMine-MP and its API evolution. It is not scoped to a separate server fork.

Implemented in this baseline:
- Native PMMP inventory lifecycle wrapper
- Menu sizes 9, 18, 27, 36, 45 and 54
- Per-player active-menu tracking
- Open, click and close callbacks
- Button-menu default that cancels item movement
- Optional item movement mode
- Standalone plugin entry point
- Virion metadata and Composer autoload metadata
- Build, CI and release workflows
- Published compatibility policy and official Tuff PMMP Discord link

Not production-certified:
- Fake chest block graphics or client-side custom title rendering
- Runtime testing on all supported PocketMine-MP builds
- Automated tests for duplication, disconnect, repeated opens and every click path

See docs/TESTING.md before using on a production server.
