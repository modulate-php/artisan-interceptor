<?php

namespace Modulate\Artisan\Interceptor\Enums;

/**
 * Defines the events and stacks that are valid for the interceptor
 */
enum StackType: string
{
    case start = 'start';
    case before = 'before';
    case after = 'after';
}