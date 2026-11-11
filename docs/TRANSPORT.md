# Virtual container transport — development notes

## Ownership model
InvMenuGUI does not allocate Bedrock window IDs or send inventory content packets itself. It registers a per-player callback in PocketMine's `NetworkSession::getInvManager()->getContainerOpenCallbacks()`. The callback returns a `ContainerOpenPacket` for the matching virtual inventory. PocketMine then owns window-ID allocation, content serialization, item-stack request tracking, transaction mapping, close acknowledgement and inventory synchronization.

## Current implementation scope
- Virtual chest and hopper block state is sent only to the viewer with `UpdateBlockPacket`.
- A per-player open callback supplies the virtual block position and container window type.
- PocketMine's inventory manager remains responsible for inventory content and slot updates.
- Closing removes the callback and resends the real world block update to that viewer.
- No server-world block is placed or mutated.

## Known gaps — do not call this production-ready
- DOUBLE_CHEST currently uses one fake chest graphic and a 54-slot logical inventory; paired chest block states and protocol-version-specific axis/left-right pairing are not implemented.
- Custom visible title rendering / block actor custom-name data is not implemented.
- The fake block position strategy requires live client tests for collision, chunk boundaries, dimension limits and player movement.
- Close acknowledgement and rapid menu replacement require integration tests on each supported Bedrock protocol.
- Multi-viewer behavior and inventory sharing must be tested under simultaneous transactions.

## Test matrix
Test CHEST (27), DOUBLE_CHEST (54), HOPPER (5), custom title, item movement cancellation, close from client, close from server, rapid menu replacement, player disconnect, world change, chunk edge, and two players viewing the same menu. Run against each target PMMP API/protocol pair.
