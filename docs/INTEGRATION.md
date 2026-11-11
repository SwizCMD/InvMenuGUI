# Integration policy

## Virion
The library is shaded into each consumer. Each consumer owns its own manager and class copy. Recommended for plugins distributed independently.

## Standalone
The Phar owns the shared service. Consumers must hard-depend on the InvMenuGUI plugin and use its instance to create menus.

## No hybrid duplicates
Do not shade the Virion into a plugin that also depends on the standalone Phar. That can create duplicate classes/managers and callbacks registered against different instances.
