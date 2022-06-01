<?php

declare(strict_types=1);

namespace Test;


use Istok\Container\Container;
use Istok\Container\Psr\NotFound;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    /** @test */
    public function it_can_set_and_get(): void
    {
        $container = new Container();
        $container->singleton('ABC', fn() => 'wer');

        $this->assertTrue($container->has('ABC'));
        $this->assertEquals('wer', $container->make('ABC'));
    }

    /** @test */
    public function it_can_resolve_params(): void
    {
        $container = new Container();
        $container->singleton('ABC', fn(NotFound $example) => $example->getMessage());
        $container->singleton(NotFound::class, fn() => new NotFound('marker'));

        $this->assertEquals('marker', $container->make('ABC'));
    }

    /** @test */
    public function it_can_give_concrete(): void
    {
        $container = new Container();
        $r = $container->make(NotFound::class);

        $this->assertEquals(new NotFound(), $r);
    }

    /** @test */
    public function it_can_use_suggested_params(): void
    {
        $container = new Container();
        $container->argument('message', NotFound::class, fn() => 'marker');

        $this->assertEquals(new NotFound('marker'), $container->make(NotFound::class));
    }
}
