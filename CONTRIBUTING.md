# Contributing

- Preserve public API compatibility where practical.
- Keep fork-specific adaptations isolated.
- Document packet-level workarounds and their rationale.
- Run `composer validate --no-check-publish`.
- Run `find src examples -name '*.php' -print0 | xargs -0 -n1 php -l`.
- Run `php -d phar.readonly=0 tools/build.php`.
- Complete `docs/TESTING.md` on a live staging server before marking a build production-ready.
