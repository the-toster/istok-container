<?php

declare(strict_types=1);

namespace Test\Psr;


use Istok\Container\Container;
use Istok\Container\Psr\GreedyContainer;
use Istok\Container\Psr\NotFound;
use PHPUnit\Framework\TestCase;

final class GreedyTest extends TestCase
{
    /** @test */
    public function it_can_check_registration(): void
    {
        $container = new GreedyContainer(new Container());
        $identifier = 'test';
        $this->assertFalse($container->has($identifier));
    }

    /** @test */
    public function has_without_registration(): void
    {
        $container = new GreedyContainer(new Container());
        $identifier = PsrTestEntry::class;
        $this->assertTrue($container->has($identifier));
    }

    /** @test */
    public function it_follow_psr_about_not_found_if_has_is_false(): void
    {
        $container = new GreedyContainer(new Container());
        $identifier = 'test';
        $this->assertFalse($container->has($identifier));
        $this->expectException(NotFound::class);
        $container->get($identifier);
    }


}
