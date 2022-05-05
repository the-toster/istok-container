<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

use ReflectionNamedType;

class StaticResolver implements ModelResolver
{
    public function __construct(
        private readonly string $typeName,
        private readonly mixed $instance,
    )
    {
    }

    public function match(ReflectionNamedType $type): bool
    {
        return $this->typeName === $type->getName();
    }

    public function resolve(ReflectionNamedType $type): mixed
    {
        return $this->instance;
    }

}
