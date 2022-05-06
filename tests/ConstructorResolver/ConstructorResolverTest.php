<?php

declare(strict_types=1);

namespace Test\ConstructorResolver;


use Istok\Container\ModelResolving\ConstructorResolver;
use PHPUnit\Framework\TestCase;
use Test\ConstructorResolver\TestObject\Item;
use Test\ConstructorResolver\TestObject\ItemList;
use Test\ConstructorResolver\TestObject\Rich;
use Test\ConstructorResolver\TestObject\Untyped;

final class ConstructorResolverTest extends TestCase
{
    /** @test */
    public function it_can_build_simple(): void
    {
        $data = ['id' => 'id1', 'title' => 'title1'];
        $r = (new ConstructorResolver())->resolve(new \ReflectionClass(Item::class), $data);
        $this->assertEquals(new Item('id1', 'title1'), $r);
    }

    /** @test */
    public function it_can_build_untyped(): void
    {
        $data = ['a' => 'ab', 'b' => 'bc'];
        $r = (new ConstructorResolver())->resolve(new \ReflectionClass(Untyped::class), $data);
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
        $r = (new ConstructorResolver())->resolve(new \ReflectionClass(Rich::class), $data);

        $expected = new Rich(
            'id1', 10, true, [1, 2, 3], [4, 5, 6],
            new Item('single', 't1'),
            new Item('multiple1', 'm1'),
            new Item('multiple2', 'm2'),
            new Item('multiple3', 'm3'),
        );


        $this->assertEquals($expected, $r);
    }

}
