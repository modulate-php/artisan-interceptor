# Modulate Artisan Interceptor
### An easy but elegant way to change global behaviours of Artisan commands

## Features

- Add new global options to artisan e.g. --tennant=2 or --module=my-module
- Add handlers to be executed before and/or after an artisan command is run
- Add conditional handlers than only run when a specified option is given to a command
- Fluent builder for adding input options to artisan

## Usage
```php
<?php
use Modulate\Artisan\Interceptor\InterceptedCommand;

// Add a new option to the console
ArtisanInterceptor::addOption(
    ArtisanInterceptor::optionBuilder()
        ->name('tenant')
        ->optional()
        ->get()
// Add a callback that runs before the command is run
// but will only run if the given option is set
)->before(function(InterceptedCommand $intercepted) {
    echo sprintf(
        'Hello from %s tenantId: %d', 
        $intercepted->getCommand(),
        $intercepted->getInput()->getOption('tenant')
    );
// Add a callback that runs before the command is run
// but will only run if the given option is set
}, 'tenant')->after(function(InterceptedCommand $intercepted) {
    echo sprintf(
        'exitCode %d',
        $intercepted->getExitCode(),
    );
// You can also omit the option parameter to a before or after
// callback to always run the callback
}, 'tenant')->after(function(InterceptedCommand $intercepted) {
    echo 'This callback will always run after a command';
});
```

## Extending Interceptor
You have full control of what the interceptor for all callback types.
You can even add your own custom handlers by implementing the handler
contracts directly

