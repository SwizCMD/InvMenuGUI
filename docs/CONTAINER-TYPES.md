# Container types — 0.2.0 development API

InvMenuGUI now defines a typed model for the three built-in Bedrock container layouts:

| Type | Slots |
|---|---:|
| CHEST | 27 |
| DOUBLE_CHEST | 54 |
| HOPPER | 5 |

`MenuType` is the canonical type enum. `TransactionDecision` provides explicit ALLOW/CANCEL callback decisions, and `DeterministicGUITransaction` provides a read-only callback snapshot.

## Compatibility status

This branch is the API foundation only. It does not yet implement the network transport for fake block graphics, Bedrock window-type negotiation, custom client title delivery, or version-specific double-chest pairing. These require implementation and runtime tests on supported PMMP/client protocol combinations. Do not mark this branch production-ready until those tests pass. The existing 0.1.x size-based Menu API remains unchanged.
