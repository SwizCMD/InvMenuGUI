# Runtime testing

## Build
Run `php -d phar.readonly=0 tools/build.php`. The release folder contains the standalone library, Virion PHAR, Virion source ZIP and the self-contained `InvMenuGUITest.phar` harness.

## Install
1. Copy `InvMenuGUITest.phar` from `build/release/` into the server's `plugins/` folder.
2. Restart the server.
3. Run `/invmenutest chest`, `/invmenutest double` and `/invmenutest hopper` in-game as an operator.

The test PHAR embeds the current InvMenuGUI source, so it does not require a separate InvMenuGUI plugin or Virion installation. Do not install the standalone library PHAR and the test PHAR together; both contain the same classes.

## Checklist
- [ ] Each layout opens with the expected slot count (27 / 54 / 5).
- [ ] Diamond, emerald, gold ingot and barrier contents are visible.
- [ ] Clicking an item reports its slot and name.
- [ ] Click callback cancels movement; items remain in place.
- [ ] Closing restores the original world block(s).
- [ ] Reopening each layout works without stale callbacks.
- [ ] Test with two players at the same time.
- [ ] Disconnect and world transfer do not leave visible fake blocks.
- [ ] Double Chest renders as one joined 54-slot chest, not two independent blocks.

Record server fork/version, protocol, PHP version, device/client version and logs for every run. Source/CI checks are not a substitute for real Bedrock client validation.
