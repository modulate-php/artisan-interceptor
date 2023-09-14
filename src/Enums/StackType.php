<?php

namespace Modulate\Artisan\Interceptor\Enums;

enum StackType: string {
    case start = 'start';
    case before = 'before';
    case after = 'after';
}