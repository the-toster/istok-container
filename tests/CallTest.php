<?php

declare(strict_types=1);

namespace Test;


use Istok\Container\Container;
use PHPUnit\Framework\TestCase;
use Test\Fixtures\ClassA;
use Test\Fixtures\ClassB;
use Test\Fixtures\RequestDTO;
use Test\Fixtures\TestResolver;

final class CallTest extends TestCase
{
    /** @test */
    public function it_can_call_with_given_arguments(): void
    {
        $container = new Container();
        $r = $container->call(fn(string $a) => $a, ['a' => 'test']);
        $this->assertEquals('test', $r);
    }

    /** @test */
    public function it_can_resolve_by_container_and_arguments(): void
    {
        $container = new Container();
        $r = $container->call(fn(string $a, ClassB $b) => [$a, $b], ['a' => 'test']);
        $this->assertEquals(['test', new ClassB(new ClassA())], $r);
    }


    /** @test */
    public function it_can_resolve_by_attribute(): void
    {
        $container = new Container();

        $container->singleton(TestResolver::class, fn() => new TestResolver(['marker' => 'test']));

        $r = $container->call(fn(RequestDTO $dto) => $dto);

        $this->assertEquals(new RequestDTO('test'), $r);
    }
}
