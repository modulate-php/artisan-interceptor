<?php

namespace Modulate\Artisan\Interceptor;

use Symfony\Component\Console\Input\InputOption;

use Closure;

class OptionBuilder
{
    protected string $name = '';

    protected string|array|null $shortcut = null;

    protected int|null $mode = null;

    protected string $description = '';

    protected string|bool|int|float|array|null $default = null;

    protected array|Closure $suggestedValues = [];

    public function name($name): OptionBuilder
    {
        $this->name = $name;

        return $this;
    }

    /*
     * @param string|array|null $shortcut The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     */
    public function shortcut(string|array $shortcut = null): OptionBuilder
    {
        $this->shortcut = $shortcut;

        return $this;
    }

    public function mode(int $mode, $clear = false): OptionBuilder
    {
        if ($clear === true) {
            $this->mode = 0;
            $this->mode |= $mode;
        } else {
            $this->mode |= $mode;
        }

        return $this;
    }

    public function hasMode(int $mode): bool
    {
        return $this->mode & $mode;
    }

    protected function removeMode(int $mode): OptionBuilder
    {
        $this->mode &= ~$mode;
        return $this;
    }

    public function required(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_REQUIRED);
        $this->removeMode(InputOption::VALUE_OPTIONAL);
        return $this;
    }

    public function optional(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_OPTIONAL);
        $this->removeMode(InputOption::VALUE_REQUIRED);
        return $this;
    }

    public function array(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_IS_ARRAY);
        return $this;
    }

    public function default($default = null): OptionBuilder
    {
        $this->default = $default;

        return $this;
    }

    public function negatable(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_NEGATABLE);
        return $this;
    }

    public function flag(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_NONE, true);
        $this->default = null;

        return $this;
    }

    public function description(string $description): OptionBuilder
    {
        $this->description = $description;

        return $this;
    }

    public function get()
    {
        return new InputOption($this->name, $this->shortcut, $this->mode, $this->description, $this->default, $this->suggestedValues);
    }

}

