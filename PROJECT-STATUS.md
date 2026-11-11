# Project status

**Version:** 0.2.0-dev.2 — integrated virtual chest test update.

Implemented:
- One-command integrated plugin test: /invmenutest
- Bundled InvMenuGUI library classes in the standalone plugin PHAR
- Virtual chest block and block-actor packet transport
- Per-player container-open callback registration
- PocketMine-owned window IDs and inventory synchronization
- Click cancellation for the sample GUI
- Delayed opening to avoid immediate chat/window transition races
- Restore of the viewer's real block state after closing

Not production-certified:
- Bedrock runtime opening must still be verified on the target server fork and protocol.
- Virtual custom titles are sent as block-actor metadata but client rendering may vary.
- Double chest and hopper layouts are not part of the single-command test path.

Target test environment: Altay 5.45.1, Protocol 2225, PHP 8.4.27.
