<?php

namespace Modulate\Artisan\Interceptor\Facades;

use Illuminate\Support\Facades\Facade;
use Modulate\Artisan\Interceptor\Contracts\Interceptor;
use Modulate\Artisan\Interceptor\OptionBuilder;

class ArtisanInterceptor extends Facade 
{
    protected static function getFacadeAccessor() 
    {
        return Interceptor::class; 
    }

    public static function optionBuilder()
    {
        return app()->make(OptionBuilder::class);
    }
}