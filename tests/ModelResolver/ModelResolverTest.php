<?php

declare(strict_types=1);

namespace Test\ModelResolver;


use Istok\Container\ModelResolving\ModelResolver;
use PHPUnit\Framework\TestCase;
use Test\ModelResolver\Fixture\BackedEnum;
use Test\ModelResolver\Fixture\PureEnum;
use Test\ModelResolver\Fixture\WithEnums;

/** @psalm-suppress PropertyNotSetInConstructor */
final class ModelResolverTest extends TestCase
{
    /** @test */
    public function it_can_resolve(): void
    {
        $resolver = new ModelResolver(['pure' => 'a', 'backed' => 'b']);
        $this->assertEquals(
            new WithEnums(BackedEnum::b, PureEnum::a),
            $resolver->resolve(WithEnums::class, '')
        );
    }
}
