<?php

namespace Modulate\Artisan\Interceptor;

use Modulate\Artisan\Interceptor\Contracts\ArtisanHandler;
use Modulate\Artisan\Interceptor\Contracts\Handler as HandlerContract;
use Modulate\Artisan\Interceptor\Contracts\HandlerStack as HandlerStackContract;

class HandlerStack implements HandlerStackContract
{
    protected $stack = [];

    public function push(HandlerContract|ArtisanHandler $handler): HandlerStackContract
    {
        $this->stack[] = $handler;
        return $this;
    }

    public function empty(): bool
    {
        return empty($this->stack);
    }

    public function current(): HandlerContract|bool
    {
        return current($this->stack);
    }

    public function next()
    {
        return next($this->stack);
    }

    public function reset(): void
    {
        reset($this->stack);
    }

    public function flush(): HandlerStackContract
    {
        $this->stack = [];

        return $this;
    }

}
