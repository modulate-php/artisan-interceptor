<?php
declare(strict_types=1);
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

use Illuminate\Support\Arr;
use Modulate\Artisan\Interceptor\Handlers\ArtisanCallbackHandler;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Traits\Macroable;

/**
 * The interceptor implements the core logic of the package and is responsible for interacting with
 * both the registered callbacks and the artisan instance
 */
class Interceptor implements InterceptorContract
{
    use Macroable;

    /**
     * @var InputOption[]
     */
    protected array $options = [];

    /**
     * @var HandlerStackContract
     */
    protected HandlerStackContract $start;

    /**
     * @var HandlerStackContract
     */
    protected HandlerStackContract $before;

    /**
     * @var HandlerStackContract
     */
    protected HandlerStackContract $after;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var bool
     */
    protected bool $started = false;
    

    /**
     * @param HandlerStackContract $start
     * @param HandlerStackContract $before
     * @param HandlerStackContract $after
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
     * @param string|null $option
     * @return InterceptorContract
     */
    public function addHandler(HandlerContract|ArtisanHandler $handler, StackType $stack = StackType::before, string $option = null): InterceptorContract
    {
        if ($handler instanceof OptionHandlerContract) {
            $handler->setOption($option);
        }
        if ($stack) {
            $this->{$stack->value}->push($handler);
        }

        return $this;
    }

    /**
     * Add a handler or callable to run on artisan start
     *
     * @param callable|ArtisanHandler $callable
     * @return InterceptorContract
     */
    public function start(callable|ArtisanHandler $callable): InterceptorContract
    {
        $handler = $callable;
        if (is_callable($callable)) {
            $handler = new ArtisanCallbackHandler($callable);
        }

        $this->addHandler($handler, StackType::start);

        return $this;
    }

    /**
     * Add a handler or callable to run on before command starts
     *
     * @param callable|HandlerContract $callable
     * @param string|null $option
     * @return InterceptorContract
     */
    public function before(callable|HandlerContract $callable, string $option = null): InterceptorContract
    {
        $handler = $callable;
        if (is_callable($callable)) {
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

    /**
     * Add a handler or callable to run on after command finishes
     *
     * @param callable|HandlerContract $callable
     * @param string $option
     * @return InterceptorContract
     */
    public function after(callable|HandlerContract $callable, string $option = null): InterceptorContract
    {
        $handler = $callable;
        if (is_callable($callable)) {
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
     * Undocumented function
     *
     * @param InputOption[] $options
     * @return Interceptor
     */
    public function addOptions(...$options): Interceptor
    {
        // Ensure we always have a single array of options
        // regardless of if an array or list of options
        // was passed through the method
        $options = Arr::wrap($options[0]);

        foreach ($options as $option) {
            $this->addOption($option);
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

    /**
     * The handleAfter method is called once the CommandFinished event
     *
     * @param InterceptedCommand $intercepted
     * @return void
     */
    public function handleAfter(InterceptedCommand $intercepted): void
    {
        $this->handle($intercepted, StackType::after);
    }
}
