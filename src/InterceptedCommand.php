<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
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

    public function getCommandInstance(): Command
    {
        return $this->getArtisan()->get($this->command);
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
     * Get an option from the command input
     *
     * @param string $name
     * @return mixed
     */
    public function getOption(string $name): mixed
    {
        return $this->getInput()->getOption($name);
    }

    /**
     * Check if an input option exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasOption(string $name): bool
    {
        return $this->getInput()->hasOption($name);
    }

    /**
     * Set an input option
     *
     * @param string $name
     * @param mixed $value
     * @return InterceptedCommand
     */
    public function setOption(string $name, mixed $value): InterceptedCommand
    {
        $this->getInput()->setOption($name, $value);

        return $this;
    }

    /**
     * Get an argument from the command input
     *
     * @param string $name
     * @return mixed
     */
    public function getArgument(string $name): mixed
    {
        return $this->getInput()->getArgument($name);
    }

    /**
     * Check if an input argument exists
     *
     * @param string $name
     * @return boolean
     */
    public function hasArgument(string $name): bool
    {
        return $this->getInput()->hasArgument($name);
    }

    /**
     * Set an input argument
     *
     * @param string $name
     * @param mixed $value
     * @return InterceptedCommand
     */
    public function setArgument(string $name, mixed $value): InterceptedCommand
    {
        $this->getInput()->setArgument($name, $value);

        return $this;
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
    public function getExitCode(): int|null
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
