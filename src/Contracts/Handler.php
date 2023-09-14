<?php

namespace Modulate\Artisan\Interceptor\Contracts;

use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Application;

interface Handler
{
    public function check(InterceptedCommand $intercepted): bool;
    public function handle(InterceptedCommand $intercepted): void;
}
