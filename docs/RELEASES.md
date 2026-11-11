# Release process

1. Update version in `VERSION`, `virion.yml`, `plugin.yml`, `composer.json` and `CHANGELOG.md`.
2. Run Composer validation and PHP syntax lint.
3. Build with `php -d phar.readonly=0 tools/build.php`.
4. Run `docs/TESTING.md` on live staging servers.
5. Push a version tag and publish a GitHub Release.
6. The release workflow attaches the standalone Phar, Virion archive and Virion source ZIP.

Do not label a release stable until the live manual test matrix passes.
