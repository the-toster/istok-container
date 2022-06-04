<?php

declare(strict_types=1);

namespace Test;


use Istok\Container\Container;
use PHPUnit\Framework\TestCase;
use Test\Fixtures\ClassA;
use Test\Fixtures\ClassB;
use Test\Fixtures\WithAttribute;
use Test\Fixtures\TestResolver;

/** @psalm-suppress PropertyNotSetInConstructor */
final class CallTest extends TestCase
{
    /** @test */
    public function it_can_call_with_given_arguments(): void
    {
        $container = new Container();
        /** @psalm-suppress MixedAssignment */
        $r = $container->call(fn(string $a) => $a, ['a' => 'test']);
        $this->assertEquals('test', $r);
    }

    /** @test */
    public function it_can_resolve_by_container_and_arguments(): void
    {
        $container = new Container();
        /** @psalm-suppress MixedAssignment */
        $r = $container->call(fn(string $a, ClassB $b) => [$a, $b], ['a' => 'test']);
        $this->assertEquals(['test', new ClassB(new ClassA())], $r);
    }


    /** @test */
    public function it_can_resolve_by_attribute(): void
    {
        $container = new Container();

        $container->singleton(TestResolver::class, fn() => new TestResolver(['marker' => 'test']));
        /** @psalm-suppress MixedAssignment */
        $r = $container->call(fn(WithAttribute $dto) => $dto);

        $this->assertEquals(new WithAttribute('test'), $r);
    }
}
