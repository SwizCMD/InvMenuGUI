# Manual test checklist

- [ ] A 27-slot virtual inventory opens.
- [ ] Contents synchronize to the player.
- [ ] Clicking a button invokes the callback.
- [ ] Button mode cancels item movement.
- [ ] Close callback runs exactly once.
- [ ] Repeated opens/closes do not leave stale sessions.
- [ ] Replacing an existing window works cleanly.
- [ ] Two players use separate sessions.
- [ ] Disconnect cleans up state.
- [ ] A cancelled open event leaves no active menu session.
- [ ] Storage mode behaves as configured.
- [ ] Repeat on every server build, fork and protocol advertised as supported.

Record server version, protocol, PHP version and test results with every compatibility report.
