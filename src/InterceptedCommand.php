<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InterceptedCommand
{

    /**
     * The command that was called
     *
     * @var string
     */
    protected string $command;
    
    /**
     * The artisan console instance
     *
     * @var Application
     */
    protected Application $app;

    /**
     * The input given to the command
     *
     * @var InputInterface
     */
    protected InputInterface $input;

    /**
     * The instance where the output of the command will be sent
     *
     * @var OutputInterface
     */
    protected OutputInterface $output;

    /**
     * The exit code of the command which will be populated when the CommandFinished event is fired
     *
     * @var integer|null
     */
    protected int|null $exitCode = 0;

    /**
     * @param string $command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param integer|null $exitCode
     */
    public function __construct(string $command, InputInterface $input, OutputInterface $output, int $exitCode = null)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
        $this->exitCode = $exitCode;
    }

    /**
     * Return the command that was intercepted
     *
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Inject the artisan application into the intercepted command
     *
     * @param Application $app
     * @return void
     */
    public function setArtisan(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the artisan console application
     *
     * @return Application
     */
    public function getArtisan(): Application
    {
        return $this->app;
    }

    /**
     * Get the input for the command
     *
     * @return InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * Get the output for the command
     *
     * @return OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * Get the exit code of the finished command
     *
     * @return integer
     */
    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    /**
     * Determines if the command is still running by the lack of an exit code
     *
     * @return boolean
     */
    public function hasFinished(): bool
    {
        return $this->exitCode !== null;
    }
}