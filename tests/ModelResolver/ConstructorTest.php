<?php

declare(strict_types=1);

namespace Test\ModelResolver;


use Istok\Container\ModelResolving\Constructor;
use PHPUnit\Framework\TestCase;
use Test\ModelResolver\Fixture\BackedEnum;
use Test\ModelResolver\Fixture\Item;
use Test\ModelResolver\Fixture\PureEnum;
use Test\ModelResolver\Fixture\Rich;
use Test\ModelResolver\Fixture\TestEnum;
use Test\ModelResolver\Fixture\Untyped;

final class ConstructorTest extends TestCase
{
    /** @test */
    public function it_can_build_simple(): void
    {
        $data = ['id' => 'id1', 'title' => 'title1'];
        $r = (new Constructor())->resolve(new \ReflectionClass(Item::class), $data);
        $this->assertEquals(new Item('id1', 'title1'), $r);
    }

    /** @test */
    public function it_can_build_untyped(): void
    {
        $data = ['a' => 'ab', 'b' => 'bc'];
        $r = (new Constructor())->resolve(new \ReflectionClass(Untyped::class), $data);
        $this->assertEquals(new Untyped('ab', 'bc'), $r);
    }

    /** @test */
    public function it_can_build_complex(): void
    {
        $data = [
            'id' => 'id1',
            'n' => 10,
            'flag' => true,
            'a1' => [1, 2, 3],
            'a2' => [4, 5, 6],
            'singleItem' => ['id' => 'single', 'title' => 't1'],
            'items' => [
                ['id' => 'multiple1', 'title' => 'm1'],
                ['id' => 'multiple2', 'title' => 'm2'],
                ['id' => 'multiple3', 'title' => 'm3'],
            ]
        ];
        $r = (new Constructor())->resolve(new \ReflectionClass(Rich::class), $data);

        $expected = new Rich(
            'id1', 10, true, [1, 2, 3], [4, 5, 6],
            new Item('single', 't1'),
            new Item('multiple1', 'm1'),
            new Item('multiple2', 'm2'),
            new Item('multiple3', 'm3'),
        );


        $this->assertEquals($expected, $r);
    }

    /** @test */
    public function it_can_resolve_enum_params(): void
    {
        $expected = new TestEnum(BackedEnum::a, PureEnum::b);
        $r = (new Constructor())->resolve(new \ReflectionClass(TestEnum::class), ['backed' => 1, 'pure' => 'b']
        );
        $this->assertEquals($expected, $r);
        $r2 = (new Constructor())->resolve(
            new \ReflectionClass(TestEnum::class),
            ['backed' => 'a', 'pure' => 'b']
        );
        $this->assertEquals($expected, $r2);
    }

}
