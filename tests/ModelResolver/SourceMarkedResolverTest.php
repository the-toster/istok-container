<?php

declare(strict_types=1);

namespace Test\ModelResolver;


use Istok\Container\ModelResolving\SourceMarkedResolver;
use PHPUnit\Framework\TestCase;
use Test\ModelResolver\TestObject\BackedEnum;
use Test\ModelResolver\TestObject\PureEnum;
use Test\ModelResolver\TestObject\TestEnum;

final class SourceMarkedResolverTest extends TestCase
{
    /** @test */
    public function it_can_resolve(): void
    {
        $resolver = new SourceMarkedResolver('test', ['pure' => 'a', 'backed' => 'b']);
        $this->assertEquals(new TestEnum(BackedEnum::b, PureEnum::a), $resolver->resolve(TestEnum::class));
    }
}
