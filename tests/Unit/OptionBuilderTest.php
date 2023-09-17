<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modulate\Artisan\Interceptor\OptionBuilder;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Exception\InvalidArgumentException;

class OptionBuilderTest extends TestCase
{
    protected OptionBuilder $builder;
    public function setUp(): void
    {
        parent::setUp();
        $this->builder = new OptionBuilder();
    }
    /**
     * A basic unit test example.
     */
    public function test_create(): void
    {
        $inputOption = $this->builder
            ->name('foo')
            ->get();
        $this->assertInstanceOf(InputOption::class, $inputOption);
    }

    public function test_flag(): void
    {
        $inputOption = $this->builder
            ->name('foo')
            ->flag()
            ->get();
        $this->assertFalse($inputOption->acceptValue());

    }
    public function test_optional(): void
    {
        $inputOption = $this->builder
            ->name('foo')
            ->optional()
            ->get();
        $this->assertFalse($inputOption->isValueRequired());

    }
    
    public function test_required(): void
    {
        $inputOption = $this->builder
            ->name('foo')
            ->required()
            ->get();
        $this->assertTrue($inputOption->isValueRequired());

    }
    
    public function test_negatable(): void
    {
        $inputOption = $this->builder
            ->name('foo')
            ->negatable()
            ->get();
        $this->assertTrue($inputOption->isNegatable());

    }
    
    public function test_accepts_array(): void
    {
        $inputOption = $this->builder
            ->name('foo')
            ->required()
            ->array()
            ->get();
        $this->assertTrue($inputOption->isArray());
    }

    public function test_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $inputOption = $this->builder
            ->name('foo')
            ->array()
            ->get();
        
        $this->expectException(InvalidArgumentException::class);
        $inputOption = $this->builder
            ->name('foo')
            ->flag()
            ->required()
            ->get();
    }

}