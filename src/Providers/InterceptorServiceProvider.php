<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\Event;
use Modulate\Artisan\Interceptor\Contracts\HandlerStack as HandlerStackContract;
use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Input\InputOption;


use Modulate\Artisan\Interceptor\Contracts\Interceptor as InterceptorContract;
use Modulate\Artisan\Interceptor\Facades\ArtisanInterceptor;
use Modulate\Artisan\Interceptor\HandlerStack;
use Modulate\Artisan\Interceptor\Interceptor;
use Modulate\Artisan\Interceptor\OptionBuilder;

class InterceptorServiceProvider extends ServiceProvider
{

    protected $artisan;

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->app->bind(HandlerStackContract::class, HandlerStack::class);
            $this->app->singleton(InterceptorContract::class, Interceptor::class);

            $this->app->bind(OptionBuilder::class, function() {
                return new OptionBuilder();
            });
        }

    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            Event::listen(ArtisanStarting::class, function(ArtisanStarting $e) {
                $this
                    ->app
                    ->make(InterceptorContract::class)
                    ->starting($e->artisan);
            });

            Event::listen(CommandStarting::class, function(CommandStarting $e) {
                $this
                    ->app
                    ->make(InterceptorContract::class)
                    ->handleBefore(new InterceptedCommand($e->command, $e->input, $e->output));
            });
            Event::listen(CommandFinished::class, function(CommandFinished $e) {
                $this
                    ->app
                    ->make(InterceptorContract::class)
                    ->handleAfter(new InterceptedCommand($e->command, $e->input, $e->output, $e->exitCode));
            });

        }
    }
}
