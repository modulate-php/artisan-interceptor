<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor\Handlers;

use Modulate\Artisan\Interceptor\Contracts\OptionHandler;
use Modulate\Artisan\Interceptor\InterceptedCommand;

use Symfony\Component\Console\Application;

class CallbackOptionHandler implements OptionHandler
{

    /**
     * @var string
     */
    protected $option;

    /**
     * @var callable
     */
    protected $callable;

    /**
     * @var Application
     */
    protected $app;

    /**
     *
     * @param string $option
     * @param callable|null $callable
     */
    public function __construct(
        string $option,
        callable $callable = null,
    ) {
        $this->option   = $option;
        $this->callable = $callable;
    }

    public function setApplication(Application $app): OptionHandler
    {
        $this->app = $app;
        return $this;
    }


    public function setOption($option): OptionHandler
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Check to see if the required option is present and has a value
     * If false is returned then the callback is not executed
     *
     * @param InterceptedCommand $intercepted
     * @return boolean
     */
    public function check(InterceptedCommand $intercepted): bool
    {
        if (
            $intercepted->getInput()->hasOption($this->option)
            && $intercepted->getInput()->getOption($this->option)
        ) {
            return true;
        }
        return false;

    }

    /**
     * The handle function will execute the stored closure
     *
     * @param InterceptedCommand $intercepted
     * @return void
     */
    public function handle(InterceptedCommand $intercepted): void
    {
        if ($this->callable) {
            call_user_func($this->callable, $intercepted);
        }
    }
}
