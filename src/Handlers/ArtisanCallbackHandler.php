<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor\Handlers;

use Modulate\Artisan\Interceptor\Contracts\ArtisanHandler;
use Symfony\Component\Console\Application;

class ArtisanCallbackHandler implements ArtisanHandler
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
     * Check is called on start to determine if the callback should be called
     *
     * @param Application $app
     * @return boolean
     */
    public function check(Application $app): bool
    {
        return true;
    }

    /**
     * Executes the passed in callback
     *
     * @param Application $intercepted
     * @return void
     */
    public function handle(Application $intercepted): void
    {
        if ($this->callable) {
            call_user_func($this->callable, $intercepted);
        }

    }
}