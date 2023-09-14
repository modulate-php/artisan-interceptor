<?php

namespace Modulate\Artisan\Interceptor\Contracts;

use Modulate\Artisan\Interceptor\Enums\StackType;
use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Application;

use Symfony\Component\Console\Input\InputOption;

interface Interceptor
{

    public function addHandler(Handler $handler, StackType $stack = StackType::before, string $option = null): Interceptor;

    public function start(callable|ArtisanHandler $callable): Interceptor;

    public function before(callable|Handler $callable, string $option = null): Interceptor;

    public function after(callable|Handler $callable, $option = null): Interceptor;

    public function handleStart(Application $artisan): void;

    public function handleBefore(InterceptedCommand $intercepted): void;

    public function handleAfter(InterceptedCommand $intercepted): void;

    public function addOption(InputOption $option);

    public function starting(Application $app): Interceptor;

    public function isStarted(): bool;
}