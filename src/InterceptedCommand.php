<?php

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

    public function __construct(string $command, InputInterface $input, OutputInterface $output, int $exitCode = null)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
        $this->exitCode = $exitCode;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setArtisan(Application $app)
    {
        $this->app = $app;
    }

    public function getArtisan(): Application
    {
        return $this->app;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    public function hasFinished(): bool
    {
        return $this->exitCode !== null;
    }
}