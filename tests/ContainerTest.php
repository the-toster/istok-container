<?php

declare(strict_types=1);

namespace Test;


use Istok\Container\Container;
use Istok\Container\TypeCheckError;
use PHPUnit\Framework\TestCase;
use Test\Fixtures\ClassA;
use Test\Fixtures\ClassB;
use Test\Fixtures\WithMarker;

final class ContainerTest extends TestCase
{
    /** @test */
    public function it_can_give_non_shared_instances(): void
    {
        $container = new Container();
        $id = 'id';
        $container->register($id, fn() => new ClassA());
        $i1 = $container->make($id);
        $i2 = $container->make($id);
        $this->assertEquals($i1, $i2); // they are equals
        $this->assertFalse($i1 === $i2); // but not the same
    }

    /** @test */
    public function it_can_give_shared_instances(): void
    {
        $container = new Container();
        $id = 'id';
        $container->singleton($id, fn() => new ClassA());
        $i1 = $container->make($id);
        $i2 = $container->make($id);
        $this->assertTrue($i1 === $i2); // they are the same thing
        // as illustration
        $this->assertFalse((new ClassA) === (new ClassA));
    }

    /** @test */
    public function it_can_set_and_get(): void
    {
        $container = new Container();
        $container->singleton('ABC', fn() => 'marker');

        $this->assertTrue($container->has('ABC'));
        $this->assertEquals('marker', $container->make('ABC'));
    }

    /** @test */
    public function it_can_resolve_params_of_definition(): void
    {
        $container = new Container();
        $container->singleton('ABC', fn(WithMarker $example) => $example->marker);
        $container->singleton(WithMarker::class, fn() => new WithMarker('definition-param'));

        $this->assertEquals('definition-param', $container->make('ABC'));
    }

    /** @test */
    public function it_can_resolve_concrete_class_without_registration(): void
    {
        $container = new Container();
        $r = $container->make(ClassA::class);

        $this->assertEquals(new ClassA(), $r);
    }

    /** @test */
    public function it_can_use_bound_arguments(): void
    {
        $container = new Container();
        $container->argument('marker', WithMarker::class, fn() => 'bound arg');

        $this->assertEquals(new WithMarker('bound arg'), $container->make(WithMarker::class));
    }

    /** @test */
    public function it_can_resolve_with_type_check(): void
    {
        $containter = new Container();
        $instance = $containter->construct(ClassA::class);
        $this->assertInstanceOf(ClassA::class, $instance);
    }

    /** @test */
    public function it_perform_type_check_of_resolved_instance(): void
    {
        $containter = new Container();
        $containter->singleton(ClassB::class, ClassA::class);
        $this->expectExceptionMessage(TypeCheckError::resolvedNonInstance(new ClassA(), ClassB::class)->getMessage());
        $containter->construct(ClassB::class);
    }

    /** @test */
    public function it_perform_type_check_of_requested_type(): void
    {
        $containter = new Container();
        $nonTypeIdentifier = 'nonTypeIdentifier';
        $this->expectExceptionMessage(TypeCheckError::requestedUnknown($nonTypeIdentifier)->getMessage());
        $containter->construct($nonTypeIdentifier);
    }
}
