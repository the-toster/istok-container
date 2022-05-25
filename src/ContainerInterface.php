<?php

declare(strict_types=1);

namespace Istok\Container;

interface ContainerInterface
{
    // resolver methods

    // PSR
    public function get(string $id): mixed;
    public function has(string $id): bool;


    /**
     * Helper to give hint for psalm, contains typechecking
     *
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    public function make(string $id): object;

    /**
     * Call any closure, use container to resolve arguments
     * @param \Closure $fn
     * @param array<string, mixed> $arguments Predefined arguments
     * @return mixed
     */
    public function call(\Closure $fn, array $arguments = []): mixed;


    // configuration methods


    public function set(string $id, string|\Closure $def): void;

    /**
     * Give argument
     */
    public function bindArgument(string $name, string $for, \Closure $resolver): void;
}
