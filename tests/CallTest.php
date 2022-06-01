<?php

declare(strict_types=1);

namespace Test;


use Istok\Container\Container;
use Istok\Container\NotResolvable;
use Istok\Container\Psr\NotFound;
use PHPUnit\Framework\TestCase;
use Test\Fixtures\ClassA;
use Test\Fixtures\ClassB;
use Test\Fixtures\RequestDTO;
use Test\Fixtures\TestResolver;

final class CallTest extends TestCase
{
    /** @test */
    public function it_can_perform_simple_resolve(): void
    {
        $container = new Container();
        $r = $container->call(fn(ClassA $a) => $a);
        $this->assertEquals(new ClassA(), $r);
    }

    /** @test */
    public function it_can_perform_complex_resolve(): void
    {
        $container = new Container();

        $container->singleton(TestResolver::class, fn() => new TestResolver(['marker' => 'mark']));

        $r = $container->call(
            fn(string $a, RequestDTO $dto, ClassA $classA, ClassB $classB, string $b = 'b') => [
                'a' => $a,
                'dto' => $dto,
                'classA' => $classA,
                'classB' => $classB,
                'b' => $b,
            ],
            ['a' => 'a'],
        );

        $this->assertEquals(
            [
                'a' => 'a',
                'dto' => new RequestDTO('mark'),
                'classA' => new ClassA(),
                'classB' => new ClassB(new ClassA()),
                'b' => 'b',
            ],
            $r
        );
    }
}
