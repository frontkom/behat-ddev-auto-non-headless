# behat-ddev-auto-non-headless

A package to make it easier to run non headless locally, without changing the committed config (so it shows up as a diff).

## Installation

Probably you want to download this as a dev package:

```
composer require --dev frontkom/behat-ddev-auto-non-headless
```

## Usage

Add something like this to your `behat.yml(.dist)` file:

```
  extensions:
    frontkom\BehatAutoDdevNonHeadless\DisableHeadlessExtension: ~
```

## Licence 

MIT
