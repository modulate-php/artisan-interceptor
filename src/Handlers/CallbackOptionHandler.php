<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor\Handlers;

use Modulate\Artisan\Interceptor\Contracts\OptionHandler;
use Modulate\Artisan\Interceptor\InterceptedCommand;

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
     *
     * @param string $option
     * @param callable $callable
     */
    public function __construct(
        string $option,
        callable $callable,
    ) {
        $this->option   = $option;
        $this->callable = $callable;
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
            // If the option has a value or does not accept a value
            && ($intercepted->getInput()->getOption($this->option)
                || !$intercepted->getArtisan()->getDefinition()->getOption($this->option)->acceptValue())
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
