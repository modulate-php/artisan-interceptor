<?php

namespace Modulate\Artisan\Interceptor\Contracts;

use Modulate\Artisan\Interceptor\Enums\StackType;
use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Application;

use Symfony\Component\Console\Input\InputOption;

interface Interceptor
{

    /**
     * Add a handler to the interceptor
     *
     * @param Handler $handler    The handler to add
     * @param StackType  $stack   The stack to add the handler to
     * @param string|null $option The option to bind the handler to
     * @return Interceptor
     */
    public function addHandler(Handler $handler, StackType $stack = StackType::before, string $option = null): Interceptor;

    /**
     * Add a start callback to the interceptor
     *
     * @param callable|ArtisanHandler $callable
     * @return Interceptor
     */
    public function start(callable|ArtisanHandler $callable): Interceptor;

    /**
     * Add a before callback to the interceptor
     *
     * @param callable|Handler $callable
     * @param string|null $option
     * @return Interceptor
     */
    public function before(callable|Handler $callable, string $option = null): Interceptor;

    /**
     * Add an after handler to the interceptor
     *
     * @param callable|Handler $callable
     * @param string|null $option
     * @return Interceptor
     */
    public function after(callable|Handler $callable, string $option = null): Interceptor;

    /**
     * Handle the artisan starting event
     *
     * @param Application $artisan
     * @return void
     */
    public function handleStart(Application $artisan): void;

    /**
     * Handle the command starting event
     *
     * @param InterceptedCommand $intercepted
     * @return void
     */
    public function handleBefore(InterceptedCommand $intercepted): void;

    /**
     * Handle the command finshed event
     *
     * @param InterceptedCommand $intercepted
     * @return void
     */
    public function handleAfter(InterceptedCommand $intercepted): void;

    /**
     * Add a global input option to the artisan console
     *
     * @param InputOption $option
     * @return void
     */
    public function addOption(InputOption $option);

    /**
     * @internal Called during the artisan boot process to inject the application into the handler
     * @param Application $app The symfony console application
     *
     * @return Interceptor
    */
    public function starting(Application $app): Interceptor;

    /**
     * Check if artisan has started
     *
     * @return boolean
     */
    public function isStarted(): bool;
}