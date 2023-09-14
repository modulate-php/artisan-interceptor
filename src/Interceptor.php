<?php

namespace Modulate\Artisan\Interceptor;

use Modulate\Artisan\Interceptor\Contracts\ArtisanHandler;
use Modulate\Artisan\Interceptor\Contracts\Interceptor as InterceptorContract;
use Modulate\Artisan\Interceptor\Contracts\HandlerStack as HandlerStackContract;
use Modulate\Artisan\Interceptor\Contracts\Handler as HandlerContract;
use Modulate\Artisan\Interceptor\Contracts\OptionHandler as OptionHandlerContract;
use Modulate\Artisan\Interceptor\Enums\StackType;
use Modulate\Artisan\Interceptor\Handlers\CallbackHandler;
use Modulate\Artisan\Interceptor\Handlers\CallbackOptionHandler;
use Symfony\Component\Console\Application;

use Symfony\Component\Console\Input\InputOption;


/**
 * Undocumented class
 */
class Interceptor implements InterceptorContract
{
    /**
     * @var InputOption[]
     */
    protected array $options = [];

    /**
     * @var HandlerStack
     */
    protected HandlerStack $start;

    /**
     * @var HandlerStack
     */
    protected HandlerStack $before;

    /**
     * @var HandlerStack
     */
    protected HandlerStack $after;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var bool
     */
    protected bool $started = false;

    /**
     * @param HandlerStackContract $stack
     */
     public function __construct(HandlerStackContract $start, HandlerStackContract $before, HandlerStackContract $after)
    {
        $this->start  = $start;
        $this->before = $before;
        $this->after  = $after;
    }

    /**
     * Add a handler to the interceptor
     *
     * @param HandlerContract $handler
     * @param string $option
     * @return InterceptorContract
     */
    public function addHandler(HandlerContract $handler, StackType $stack = StackType::before, string $option = null): InterceptorContract
    {
        if ($handler instanceof OptionHandlerContract) {
            $handler->setOption($option);
        }
        if ($stack) {
            $this->{$stack->value}->push($handler);
        }

        return $this;
    }

    public function start(callable|ArtisanHandler $callable): InterceptorContract
    {
        $handler = $callable;
        if (is_callable($callable)) {
            $class = CallbackHandler::class;
            $handler = new CallbackHandler($callable);
        }

        $this->addHandler($handler, StackType::start);

        return $this;
    }    

    public function before(callable|HandlerContract $callable, string $option = null): InterceptorContract
    {
        $handler = $callable;
        if (is_callable($callable)){
            $class = CallbackHandler::class;
            if ($option) {
                $handler = new CallbackOptionHandler(
                    $option,
                    $callable
                );
            } else {
                $handler = new CallbackHandler($callable);
            }
        }

        $this->addHandler($handler, StackType::before, $option);

        return $this;
    }
    public function after(callable|HandlerContract $callable, $option = null): InterceptorContract
    {
        $handler = $callable;
        if (is_callable($callable)){
            $class = CallbackHandler::class;
            if ($option) {
                $handler = new CallbackOptionHandler(
                    $option,
                    $callable
                );
            } else {
                $handler = new CallbackHandler($callable);
            }
        }

        $this->addHandler($handler, StackType::after, $option);

        return $this;
    }

    /**
     * Adds an option to the application
     * @param InputOption $option The input option to add
     *
     * @return Interceptor
     */
    public function addOption(InputOption $option): Interceptor
    {
        // If the app has not yet started store a reference to the option to
        // add to the options array for adding after artisan has started,
        // otherwise add it directly to the app
        if ($this->started === false) {
            $this->options[] = $option;
        } else {
            $this->app->getDefinition()->addOption($option);
        }

        return $this;
    }

    /**
     * @internal
     * Called during the artisan boot process to inject the application into the handler
     * @param Application $app The symfony console application
     *
     * @return Interceptor
     */
    public function starting(Application $app): Interceptor
    {
        $this->app = $app;
        foreach ($this->options as $option) {
            
            $this->app->getDefinition()->addOption($option);
        }

        $this->options = [];

        $this->started = true;

        $this->handleStart($app);

        return $this;
    }

    /**
     * Check to see if artisan has been started
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @internal This method is only exposed for use in unit tests. Do not rely on it for your application and instead register a start handler
     *
     * @return Application
     */
    public function getArtisan(): Application
    {
        if (!$this->isStarted()) {
            throw new \RuntimeException('Artisan cannot be returned before the interceptor has started');
        }

        return $this->app;
    }

    protected function handle(
        InterceptedCommand|Application $intercepted,
        StackType $stackType, 
    ) {
        $stack = $stackType->value;
        // Move the stack pointer back to the start
        $this->$stack->reset();
        
        if (method_exists($intercepted, 'setArtisan')) {
            $intercepted->setArtisan($this->app);
        }


        if (!$this->$stack->empty()) {
            do {
                $current = $this->$stack->current();
                if ($current instanceof OptionHandlerContract) {
                    if (!$current->check($intercepted)) {
                        continue;
                    }
                }
                $current->handle($intercepted);
            } while ($this->$stack->next());
        }
    }

    public function handleStart(Application $artisan): void
    {
        $this->handle($artisan, StackType::start);
    }

    public function handleBefore(InterceptedCommand $intercepted): void
    {
        $this->handle($intercepted, StackType::before);
    }

    public function handleAfter(InterceptedCommand $intercepted): void
    {
        $this->handle($intercepted, StackType::after);
    }
}
