<?php

namespace Workbench\App\Tests\Unit;

use Modulate\Artisan\Interceptor\HandlerStack;
use Modulate\Artisan\Interceptor\Handlers\CallbackHandler;
use PHPUnit\Framework\TestCase;

class HandlerStackTest extends TestCase
{
    protected HandlerStack $stack;

    public function setUp(): void
    {
        $this->stack = new HandlerStack();
    }
    /**
     * A basic unit test example.
     */
    public function test_empty(): void
    {
        $this->assertTrue($this->stack->empty());
    }

    public function test_add_handler(): void
    {
        $this->stack->push(new CallbackHandler(
            fn() => []
        ));

        $this->assertTrue(!$this->stack->empty());
    }
   
   public function test_flush(): void
    {
        $this->stack->push(new CallbackHandler(
            fn() => []
        ));

        $this->assertTrue(!$this->stack->empty());
        $this->stack->flush();
        $this->assertTrue($this->stack->empty());
    }
    
}
