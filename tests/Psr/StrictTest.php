<?php

declare(strict_types=1);

namespace Test\Psr;


use Istok\Container\Container;
use Istok\Container\Psr\NotFound;
use Istok\Container\Psr\StrictContainer;
use PHPUnit\Framework\TestCase;

final class StrictTest extends TestCase
{
    /** @test */
    public function it_can_check_registration(): void
    {
        $container = new StrictContainer(new Container());
        $identifier = 'test';
        $this->assertFalse($container->has($identifier));

        $container->singleton($identifier, fn() => 'entry');
        $this->assertTrue($container->has($identifier));
    }

    /** @test */
    public function it_throws_if_not_has(): void
    {
        $container = new StrictContainer(new Container());
        $identifier = PsrTestEntry::class;
        $this->assertFalse($container->has($identifier));
        $this->expectException(NotFound::class);
        $container->get($identifier);
    }

    /** @test */
    public function it_can_return_happy(): void
    {
        $container = new StrictContainer(new Container());
        $identifier = 'test';
        $entry = 'entry';
        $container->singleton($identifier, fn() => $entry);
        $this->assertEquals($entry, $container->get($identifier));
    }


}
