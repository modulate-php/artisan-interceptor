<?php

namespace Tests\Feature;

use Illuminate\Contracts\Console\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Modulate\Artisan\Interceptor\Contracts\Interceptor;
use Modulate\Artisan\Interceptor\InterceptedCommand;
use Modulate\Artisan\Interceptor\OptionBuilder;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Console\Input\InputOption;
use Modulate\Artisan\Interceptor\Facades\ArtisanInterceptor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InterceptorTest extends TestCase
{
    protected $enablesPackageDiscoveries = true; 

    public function setUp(): void
    {
        parent::setUp();
        $this->app->make(Interceptor::class);
    }

    /**
     * A basic feature test example.
     */
    public function test_add_option(): void
    {
        $builder = new OptionBuilder();
        $inputOption = $builder
            ->name('foo')
            ->get();
        ArtisanInterceptor::addOption($inputOption);
        $this->artisan('list');
        $artisan = ArtisanInterceptor::getArtisan();
        $this->assertTrue($artisan->getDefinition()->hasOption('foo'));
    }
    
    public function test_add_options(): void
    {
        $inputOption1 = ArtisanInterceptor::optionBuilder()
            ->name('username')
            ->required()
            ->get();

        $inputOption2 = ArtisanInterceptor::optionBuilder()
            ->name('password')
            ->required()
            ->get();

        ArtisanInterceptor::addOptions([ $inputOption1, $inputOption2 ]);
        
        // Call a command to start the interceptor
        $this->artisan('list');

        $artisan = ArtisanInterceptor::getArtisan();
        $this->assertTrue($artisan->getDefinition()->hasOption('username'));
        $this->assertTrue($artisan->getDefinition()->hasOption('password'));
    }
    public function test_start_callback(): void
    {
        $started = false;

        $func = function() use(&$started) {
            $started = true;
        };

        // Ensure the symfony events are wired up for the test so CommandStarting and CommandFinished events are dispatched correctly
        // as this is disabled in unit tests by default
        $this->app->make(Kernel::class)->rerouteSymfonyCommandEvents();

        ArtisanInterceptor::start($func);

        $this->artisan('list');

        $this->assertTrue($started);
    }
    
    public function test_before_callback(): void
    {

        $func = function(InterceptedCommand $command) {
            $command->getOutput()->write('intercepted');
        };

        // Ensure the symfony events are wired up for the test so CommandStarting and CommandFinished events are dispatched correctly
        // as this is disabled in unit tests by default
        $this->app->make(Kernel::class)->rerouteSymfonyCommandEvents();

        ArtisanInterceptor::before($func);

        $this->artisan('list')
            ->expectsOutputToContain('intercepted');

    }
    public function test_after_callback(): void
    {

        $func = function(InterceptedCommand $command) {
            $command->getOutput()->write('the end');
        };

        // Ensure the symfony events are wired up for the test so CommandStarting and CommandFinished events are dispatched correctly
        // as this is disabled in unit tests by default
        $this->app->make(Kernel::class)->rerouteSymfonyCommandEvents();

        ArtisanInterceptor::before($func);

        $this->artisan('list')
            ->expectsOutputToContain('the end');
    }
    
    public function test_option_callback(): void
    {
        
        $inputOption = ArtisanInterceptor::optionBuilder()
            ->name('foo')
            ->optional()
            ->get();
        ArtisanInterceptor::addOption($inputOption);

        $func = function(InterceptedCommand $command) {
            $command->getOutput()->write('has foo');
        };

        // Ensure the symfony events are wired up for the test so CommandStarting and CommandFinished events are dispatched correctly
        // as this is disabled in unit tests by default
        $this->app->make(Kernel::class)->rerouteSymfonyCommandEvents();

        ArtisanInterceptor::before($func, 'foo');

        $this->artisan('list --foo=bar')
            ->expectsOutputToContain('has foo');
        
        $this->artisan('list')
            ->doesntExpectOutputToContain('has foo');
    }
    
    public function test_intercepted(): void
    {
        $startCalled = false;
        $start = function(Application $app) use (&$startCalled) {
            $this->assertInstanceOf(Application::class, $app);
            $startCalled = true;
        };
        
        $beforeCalled = false;
        $before = function(InterceptedCommand $command) use (&$beforeCalled) {
            $this->assertInstanceOf(InterceptedCommand::class, $command);
            $this->assertTrue($command->getCommand() == 'list');
            $this->assertInstanceOf(Command::class, $command->getCommandInstance());
            $this->assertInstanceOf(Application::class, $command->getArtisan());
            $this->assertInstanceOf(InputInterface::class, $command->getInput());
            $this->assertInstanceOf(OutputInterface::class, $command->getOutput());
            $beforeCalled = true;
        };

        $afterCalled = false;
        $after = function(InterceptedCommand $command) use (&$afterCalled) {
            $this->assertEquals($command->getExitCode(), 0);
            $this->assertTrue($command->hasFinished());
            $afterCalled = true;
        };

        // Ensure the symfony events are wired up for the test so CommandStarting and CommandFinished events are dispatched correctly
        // as this is disabled in unit tests by default
        $this->app->make(Kernel::class)->rerouteSymfonyCommandEvents();

        ArtisanInterceptor::start($start);
        ArtisanInterceptor::before($before);
        ArtisanInterceptor::after($after);

        $this->artisan('list');

        $this->assertTrue($startCalled);
        $this->assertTrue($beforeCalled);
        $this->assertTrue($afterCalled);
    }
    
    public function test_wrapped(): void
    {
        $builder = new OptionBuilder();
        $inputOption = $builder
            ->name('foo')
            ->optional()
            ->default('bar')
            ->get();

        ArtisanInterceptor::addOption($inputOption);
        
        $before = function(InterceptedCommand $command) {
            $this->assertEquals($command->getOption('foo'), 'bar');
            $this->assertTrue($command->hasOption('foo'));
            $command->setOption('foo', 'baz');
            $this->assertEquals($command->getOption('foo'), 'baz');
            $this->assertEquals($command->getArgument('config'), 'app.env');
            $this->assertTrue($command->hasArgument('config'));
            $command->setArgument('config', 'app.name');
            $this->assertEquals($command->getArgument('config'), 'app.name');
        };

        // Ensure the symfony events are wired up for the test so CommandStarting and CommandFinished events are dispatched correctly
        // as this is disabled in unit tests by default
        $this->app->make(Kernel::class)->rerouteSymfonyCommandEvents();

        ArtisanInterceptor::before($before);

        $this->artisan('config:show app.env --foo=bar');
    }
}
