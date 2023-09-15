<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor\Handlers;

use Modulate\Artisan\Interceptor\Contracts\Handler;

use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Application;
use LogicException;
class CallbackHandler implements Handler
{

    /**
     * @var callable
     */
    protected $callable;


    public function __construct(
        callable $callable,
    ) {
        $this->callable = $callable;
    }

    /**
     * The check method is called to determine if the closure should be called.
     * In the case of the callback handler it will always return true
     *
     * @param InterceptedCommand $intercepted
     * @return boolean
     */
    public function check(InterceptedCommand $intercepted): bool 
    {
        return true;
    }

    public function handle(InterceptedCommand $intercepted): void
    {
        if ($this->callable) {
            call_user_func($this->callable, $intercepted);
        }
    }
}
