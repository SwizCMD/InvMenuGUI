# Build instructions

Requirements: PHP CLI with the Phar extension enabled. Composer is recommended for metadata validation.

```bash
composer validate --no-check-publish
find src examples -type f -name '*.php' -print0 | xargs -0 -n1 php -l
php -d phar.readonly=0 tools/build.php
```

Artifacts are emitted in `build/release/`:
- `InvMenuGUI.phar` — standalone plugin
- `InvMenuGUI-Virion.phar` — Virion package archive
- `InvMenuGUI-Virion-Source.zip` — Virion source layout

The Virion source ZIP is the source-oriented distribution. Check your Virion compiler's expected input format before integrating.
