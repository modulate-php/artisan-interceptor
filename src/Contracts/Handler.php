<?php

namespace Modulate\Artisan\Interceptor\Contracts;

use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Application;

interface Handler
{
    /**
     * The check method is used to determine if a handler should be called for
     * a given intercepted command
     *
     * @param InterceptedCommand $intercepted
     * @return boolean
     */
    public function check(InterceptedCommand $intercepted): bool;

    /**
     * The handle method is given the intercepted command in order
     * to perform it's logic
     *
     * @param InterceptedCommand $intercepted
     * @return void
     */
    public function handle(InterceptedCommand $intercepted): void;
}
