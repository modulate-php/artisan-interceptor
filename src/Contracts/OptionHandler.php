<?php

namespace Modulate\Artisan\Interceptor\Contracts;

use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Application;

interface OptionHandler extends Handler
{
    /**
     * Set the option that is required to be checked
     *
     * @param string $option
     * @return OptionHandler
     */
    public function setOption(string $option): OptionHandler;
}

