<?php

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

    public function handle(InterceptedCommand|Application $intercepted): void
    {
        if ($this->callable) {
            call_user_func($this->callable, $intercepted);
        }
    }
}
