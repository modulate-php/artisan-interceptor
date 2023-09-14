<?php

namespace Modulate\Artisan\Interceptor\Handlers;

use Modulate\Artisan\Interceptor\Contracts\ArtisanHandler;
use Symfony\Component\Console\Application;

class ArtisanCallbackHandler implements ArtisanHandler
{
    public function check(Application $app): bool
    {
        return true;
    }

    public function handle(Application $intercepted): void
    {
        
    }
}