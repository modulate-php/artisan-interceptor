# Modulate Artisan Interceptor
### An easy but elegant way to change global behaviours of Artisan commands

[![Latest Version on Packagist](https://img.shields.io/packagist/v/modulate/artisan-interceptor.svg?style=flat-square)](https://packagist.org/packages/modulate/artisan-interceptor)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/modulate-php/artisan-interceptor/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/modulate-php/artisan-interceptor/?branch=main)
[![Code Coverage](https://scrutinizer-ci.com/g/modulate-php/artisan-interceptor/badges/coverage.png?b=main)](https://scrutinizer-ci.com/g/modulate-php/artisan-interceptor/?branch=main)
[![Build Status](https://scrutinizer-ci.com/g/modulate-php/artisan-interceptor/badges/build.png?b=main)](https://scrutinizer-ci.com/g/modulate-php/artisan-interceptor/build-status/main)
[![Total Downloads](https://img.shields.io/packagist/dt/modulate/artisan-interceptor.svg?style=flat-square)](https://packagist.org/packages/modulate/artisan-interceptor)

## Features

- Add new global options to artisan e.g. --tenant=2 or --module=my-module
- Add handlers to be executed before and/or after an artisan command is run
- Add conditional handlers than only run when a specified option is given to a command
- Fluent builder for adding input options to artisan

## Adding Global Options
Generally speaking there is currently no easy way to add new global options to the artisan command. Options like --env or --version come built in
but artisan doesn't expose a way for you to add new ones out of the box. This is where artisan interceptor comes in.

The interceptor allows you to add new global options to artisan and add your own custom handlers detect and process those options.
This is all done using the built in artisan events but gives you a clean and elegant way of adding and interacting with new options

## Installation

You can install the package via composer:

```bash
composer require modulate/artisan-interceptor
```

## Usage

### Adding Global Options
```php
// Add a new optional option to artisan
ArtisanInterceptor::addOption(
    ArtisanInterceptor::optionBuilder()
        ->name('tenant')
        ->optional()
        ->get()
);

// Adding required options to the shell to handle things like authentication
ArtisanInterceptor::addOptions(
    ArtisanInterceptor::optionBuilder()
        ->name('user')
        ->required()
        ->get(),
    ArtisanInterceptor::optionBuilder()
        ->name('password')
        ->required()
        ->get()
);
```

### Adding Listeners
```php
<?php
use Modulate\Artisan\Interceptor\InterceptedCommand;

ArtisanInterceptor::before(function(InterceptedCommand $intercepted) {
    // Add a callback that runs before the command is run
    // but will only run if the given option is set
    $intercepted->getOutput()->writeln(sprintf(
        'Hello from %s tenantId: %d', 
        $intercepted->getCommand(),
        $intercepted->getInput()->getOption('tenant')
    ));
}, 'tenant')
->after(function(InterceptedCommand $intercepted) {
    // Add a callback that runs after the command is run
    // but will only run if the given option is set
    $intercepted->getOutput()->writeln(sprintf(
        'exitCode %d',
        $intercepted->getExitCode(),
    ));
}, 'tenant')
->after(function(InterceptedCommand $intercepted) {
    // You can also omit the option parameter to a before or after
    // callback to always run the callback
    $intercepted->getOutput()->writeln('This callback will always run after a command');
});
```

## Extending Interceptor
You have full control of what the interceptor for all callback types.
You can even add your own custom handlers by implementing the handler
contracts directly

## Testing
```bash
vendor/bin/testbench package:test
```
