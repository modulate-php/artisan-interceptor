<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor;

use Illuminate\Foundation\Exceptions\Handler;
use Modulate\Artisan\Interceptor\Contracts\ArtisanHandler;
use Modulate\Artisan\Interceptor\Contracts\Handler as HandlerContract;
use Modulate\Artisan\Interceptor\Contracts\HandlerStack as HandlerStackContract;

class HandlerStack implements HandlerStackContract
{
    /**
     * Stores the handlers in the stack
     *
     * @var HandlerContract[]|ArtisanHandler[]
     */
    protected array $stack = [];

    /**
     * @inheritDoc
     */
    public function push(HandlerContract|ArtisanHandler $handler): HandlerStackContract
    {
        $this->stack[] = $handler;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function empty(): bool
    {
        return empty($this->stack);
    }

    /**
     * @inheritDoc
     */
    public function current(): HandlerContract|bool
    {
        return current($this->stack);
    }

    /**
     * @inheritDoc
     */
    public function next(): HandlerContract|ArtisanHandler|bool
    {
        return next($this->stack);
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        reset($this->stack);
    }

    /**
     * @inheritDoc
     */
    public function flush(): HandlerStackContract
    {
        $this->stack = [];

        return $this;
    }

}
