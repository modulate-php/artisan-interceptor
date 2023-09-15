<?php
declare(strict_types=1);
namespace Modulate\Artisan\Interceptor;

use Symfony\Component\Console\Input\InputOption;

use Closure;

class OptionBuilder
{
    /**
     * The name of the option
     * @var string
     */
    protected string $name = '';

    /**
     * The shortcut(s) for the option
     *
     * @var string|array|null
     */
    protected string|array|null $shortcut = null;

    /**
     * The mode (mask) of the option
     * @var int|null
     */
    protected int|null $mode = null;

    /**
     * The description of the option
     * @var string
     */
    protected string $description = '';

    /**
     * The default value of the option (must be null for VALUE_NEGATABLE and VALUE_NONE modes)
     */
    protected string|bool|int|float|array|null $default = null;

    /**
     * An array of suggested values or a closure returning the suggested values
     * @var array|Closure
     */
    protected array|Closure $suggestedValues = [];

    /**
     * @param string $name the name of the option
     *
     * @return OptionBuilder
     */
    public function name($name): OptionBuilder
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string|array|null $shortcut The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     */
    public function shortcut(string|array $shortcut = null): OptionBuilder
    {
        $this->shortcut = $shortcut;

        return $this;
    }

    /**
     * Adds a mode mask option to the option
     * See InputOption::VALUE_*
     *
     * @param integer $mode  The mode to use
     * @param boolean $clear Whether to reset the existing mode (as to prevent an invalid exception being thrown by the InputOption)
     * @return OptionBuilder
     */
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

    /**
     * Checks if the bit is active in the current mask
     *
     * @param integer $mode
     * @return boolean
     */
    public function hasMode(int $mode): bool
    {
        return $this->mode & $mode ? true : false;
    }

    /**
     * Removes the mode from the current mask
     *
     * @param integer $mode
     * @return OptionBuilder
     */
    protected function removeMode(int $mode): OptionBuilder
    {
        $this->mode &= ~$mode;
        return $this;
    }

    /**
     * Mark the option as reqired
     *
     * @return OptionBuilder
     */
    public function required(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_REQUIRED);
        $this->removeMode(InputOption::VALUE_OPTIONAL);
        return $this;
    }

    /**
     * Mark the option as required
     *
     * @return OptionBuilder
     */
    public function optional(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_OPTIONAL);
        $this->removeMode(InputOption::VALUE_REQUIRED);
        return $this;
    }

    /**
     * Mark the option as accepting an array
     *
     * @return OptionBuilder
     */
    public function array(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_IS_ARRAY);
        return $this;
    }

    /**
     * Provide a default value for the option
     *
     * @param array|bool|float|int|string|null $default
     * @return OptionBuilder
     */
    public function default(array|bool|float|int|string|null $default = null): OptionBuilder
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Mark the option as negatable e.g. --foo and --no-foo
     *
     * @return OptionBuilder
     */
    public function negatable(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_NEGATABLE);
        return $this;
    }

    /**
     * Mark the option as not accepting input and only being a flag e.g. --yes
     *
     * @return OptionBuilder
     */
    public function flag(): OptionBuilder
    {
        $this->mode(InputOption::VALUE_NONE, true);
        $this->default = null;

        return $this;
    }

    /**
     * Set a description for the option
     *
     * @param string $description
     * @return OptionBuilder
     */
    public function description(string $description): OptionBuilder
    {
        $this->description = $description;

        return $this;
    }


    /**
     * Return the built option
     *
     * @return InputOption
     */
    public function get(): InputOption
    {
        return new InputOption($this->name, $this->shortcut, $this->mode, $this->description, $this->default, $this->suggestedValues);
    }

}