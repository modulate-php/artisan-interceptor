<?php

namespace Modulate\Artisan\Interceptor\Contracts;

use Modulate\Artisan\Interceptor\InterceptedCommand;
use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Application;

interface OptionHandler extends Handler
{
    public function setOption($option);

    /**
     * The check option is called when the handler is pulled from the stack to
     * determine if this handler should be executed. This allows selective
     * control over whether or not to run the handler based on the command
     * input
     *
     * @param InputInterface $input The input from the artisan command
     * @return boolean
     */
}

