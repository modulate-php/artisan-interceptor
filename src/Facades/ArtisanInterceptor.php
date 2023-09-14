<?php

namespace Modulate\Artisan\Interceptor\Facades;

use Illuminate\Support\Facades\Facade;
use Modulate\Artisan\Interceptor\Contracts\Interceptor;
use Modulate\Artisan\Interceptor\OptionBuilder;

class ArtisanInterceptor extends Facade 
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor() 
    {
        return Interceptor::class; 
    }

    /**
     * Shortcut for getting an instance of the option builder
     *
     * @return OptionBuilder
     */
    public static function optionBuilder(): OptionBuilder
    {
        return app()->make(OptionBuilder::class);
    }
}