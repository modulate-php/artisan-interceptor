<?php

namespace Modulate\Artisan\Interceptor\Contracts;

use Symfony\Component\Console\Application;

interface ArtisanHandler
{
    public function check(Application $intercepted): bool;
    public function handle(Application $intercepted): void;
}