# Virtual container transport

## Ownership model

InvMenuGUI does not allocate Bedrock window IDs or send inventory content packets. It registers a per-player callback in PocketMine's NetworkSession InventoryManager container-open callbacks. The callback returns a ContainerOpenPacket for the matching virtual inventory. PocketMine remains responsible for window IDs, inventory content serialization, item-stack request tracking and transaction synchronization.

## Chest
- Sends one fake chest block state to the viewer.
- The block is sent only to the viewer; the server world is not mutated.
- The container-open callback points to that virtual block position.

## Double Chest
- Sends two adjacent fake chest block states to the viewer.
- Both blocks receive the same horizontal facing state.
- Sends reciprocal BlockActorDataPacket payloads using the Chest tile identity and reciprocal pairx / pairz coordinates.
- The open callback points to the primary half and opens one logical 54-slot inventory.
- Closing restores both real world block updates to the viewer.

The pair metadata follows PocketMine's native pocketmine\\block\\tile\\Chest representation (pairx and pairz). PMMP's native Chest block state itself only exposes horizontal facing; the pair relationship is tile/block-actor data, not an invented internal chest-left/chest-right block property.

## Hopper
- Sends one fake hopper block state to the viewer.
- Uses the Hopper Bedrock window type.
- The logical inventory has five slots.

## Session cleanup
The transport stores the virtual positions and callback per viewer. Closing removes the callback and restores every fake position when the player remains in the source world. After a world transfer, it avoids sending old-world block updates into the new dimension.

## Known validation requirements

This implementation is source-level work and is not yet certified by a live Bedrock client. Test the following on each advertised protocol/server build:
- Double chest visually renders as one connected two-block chest.
- Both halves open the same 54-slot inventory.
- Opening either half does not create a second independent inventory.
- North, south, east and west chest facings pair correctly.
- Pair metadata is accepted by clients using each supported Bedrock protocol.
- Both original blocks are restored after server-side close and client-side close.
- Restoration works on menu replacement and disconnect.
- Chunk-edge and world-height boundaries do not leave fake blocks visible.
- Two different viewers can use independent virtual sessions.
- Inventory transaction and item-stack request synchronization remains owned by PocketMine.

Custom visible titles and BlockActorData custom-name rendering are not implemented by this transport revision.