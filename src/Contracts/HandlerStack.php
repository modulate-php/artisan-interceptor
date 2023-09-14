<?php

namespace Modulate\Artisan\Interceptor\Contracts;

interface HandlerStack
{
    public function push(Handler|ArtisanHandler $handler): HandlerStack;

    public function empty(): bool;

    public function current();

    public function next();

    public function reset(): void;

    public function flush(): HandlerStack;


}
