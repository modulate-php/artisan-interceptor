<?php

namespace Modulate\Artisan\Interceptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InterceptedCommand
{

    protected string $command;
    
    protected Application $app;

    protected InputInterface $input;

    protected OutputInterface $output;

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

    public function isRunning(): bool
    {
        return $this->exitCode === null;
    }
}