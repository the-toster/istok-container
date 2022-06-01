<?php

declare(strict_types=1);

namespace Test\ModelResolver;


use Istok\Container\ModelResolving\Constructor;
use PHPUnit\Framework\TestCase;
use Test\ModelResolver\Fixture\BackedEnum;
use Test\ModelResolver\Fixture\Item;
use Test\ModelResolver\Fixture\WithArray;
use Test\ModelResolver\Fixture\WithScalars;
use Test\ModelResolver\Fixture\PureEnum;
use Test\ModelResolver\Fixture\WithEnums;
use Test\ModelResolver\Fixture\Untyped;
use Test\ModelResolver\Fixture\WithVariadic;

final class ConstructorTest extends TestCase
{
    /** @test */
    public function it_can_build_scalar(): void
    {
        $data = ['id' => 1, 'title' => 'title1', 'val' => 3.3, 'flag' => true];
        $r = (new Constructor())->resolve(new \ReflectionClass(WithScalars::class), $data);
        $this->assertEquals(new WithScalars(1, 'title1', 3.3, true), $r);
    }

    /** @test */
    public function it_can_build_with_arrays(): void
    {
        $data = ['a1' => [1, 2, 3], 'a2' => [4, 5, 'test']];
        $r = (new Constructor())->resolve(new \ReflectionClass(WithArray::class), $data);
        $this->assertEquals(new WithArray($data['a1'], $data['a2']), $r);
    }

    /** @test */
    public function it_can_build_variadic(): void
    {
        $data = [
            'a' => 'abc',
            'items' => [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3],
            ]
        ];
        $r = (new Constructor())->resolve(new \ReflectionClass(WithVariadic::class), $data);
        $this->assertEquals(new WithVariadic('abc', new Item(1), new Item(2), new Item(3)), $r);
    }

    /** @test */
    public function it_can_resolve_enum_params(): void
    {
        $expected = new WithEnums(BackedEnum::a, PureEnum::b);
        $r = (new Constructor())->resolve(new \ReflectionClass(WithEnums::class), ['backed' => 1, 'pure' => 'b']
        );
        $this->assertEquals($expected, $r);
        $r2 = (new Constructor())->resolve(
            new \ReflectionClass(WithEnums::class),
            ['backed' => 'a', 'pure' => 'b']
        );
        $this->assertEquals($expected, $r2);
    }

    /** @test */
    public function it_can_build_untyped(): void
    {
        $data = ['a' => 'ab', 'b' => 'bc'];
        $r = (new Constructor())->resolve(new \ReflectionClass(Untyped::class), $data);
        $this->assertEquals(new Untyped('ab', 'bc'), $r);
    }
}
