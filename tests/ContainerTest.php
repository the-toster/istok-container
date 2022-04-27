<?php

declare(strict_types=1);

namespace Test;


use PHPUnit\Framework\TestCase;
use TheToster\Container\Container;
use TheToster\Container\NotFound;

final class ContainerTest extends TestCase
{
    /** @test */
    public function it_can_set_and_get(): void
    {
        $container = new Container();
        $container->set('ABC', fn() => 'wer');

        $this->assertTrue($container->has('ABC'));
        $this->assertEquals('wer', $container->get('ABC'));
    }

    /** @test */
    public function it_can_resolve_params(): void
    {
        $container = new Container();
        $container->set('ABC', fn(NotFound $example) => $example->getMessage());
        $container->set(NotFound::class, fn() => new NotFound('marker'));

        $this->assertEquals('marker', $container->get('ABC'));
    }

    /** @test */
    public function it_can_give_concrete(): void
    {
        $container = new Container();
        $r = $container->get(NotFound::class);

        $this->assertEquals(new NotFound(), $r);
    }
}
