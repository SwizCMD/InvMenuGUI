# Project status

**Version:** 0.1.1 — development baseline.

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

Not production-certified:
- Fake chest block graphics or client-side custom title rendering
- Runtime testing on PMMP and Altay
- Fork/protocol-specific behavior
- Automated tests for duplication, disconnect, repeated opens and every click path

See docs/TESTING.md before using on a production server.
