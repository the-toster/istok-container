<?php

declare(strict_types=1);

namespace Test;


use Istok\Container\Container;
use Istok\Container\NotResolvable;
use Istok\Container\Psr\NotFound;
use PHPUnit\Framework\TestCase;

final class CallTest extends TestCase
{
    /** @test */
    public function it_can_perform_simple_resolve(): void
    {
        $container = new Container();
        $r = $container->call(fn(NotFound $a) => $a);
        $this->assertEquals(new NotFound(), $r);
    }

    /** @test */
    public function it_can_perform_complex_resolve(): void
    {
        $container = new Container();

        $r = $container->call(
            fn(string $a, NotFound $notFound, NotResolvable $notResolvable, string $b = 'b') => [
                'a' => $a,
                'b' => $b,
                'e1' => $notFound,
                'e2' => $notResolvable
            ],
            ['a' => 'a'],
        );

        $this->assertEquals(['a' => 'a', 'b' => 'b', 'e1' => new NotFound('marker'), 'e2' => new NotResolvable()], $r);
    }
}
