# Upgrade Guide

## Upgrading to `3.0` from `2.x`

This is a new major release with minor breaking changes.

### Dropped support for Laravel versions <11.33.0 and php versions <8.2
######  Likelihood Of Impact: High

Support begins from Laravel version 11.33.0 and onwards due to changes in the Laravel framework.

### Conflict with nesbot/carbon < 3.5
######  Likelihood Of Impact: Medium

In order to support php 8.4, this library requires nesbot/carbon >= 3.5

### New options where added to the config
######  Likelihood Of Impact: Low

We have introduced new configuration options, to ensure your configuration file includes these new options, delete your current `config/modeltyper.php` file, then publish the new config file using the following command.

```bash
php artisan vendor:publish --provider="FumeApp\ModelTyper\ModelTyperServiceProvider" --tag=config
```

### Removed `all` Option from `model:typer` command
######  Likelihood Of Impact: Low

use `--plurals --api-resources` instead, or set them in config

### Removed `resolve-abstract` and `throws-exceptions` Option from `model:typer` command
######  Likelihood Of Impact: Low

ModelTyper now only considers models that extends `Eloquent\Model`

### Remove ShowModelCommand command
######  Likelihood Of Impact: Very Low

`model:typer-show` command was removed and is no longer available.

### Remove Deprecated clases
######  Likelihood Of Impact: Very Low

`FumeApp\ModelTyper\ModelInterface` and `FumeApp\ModelTyper\TypescriptInterface` where removed and are no longer available.



