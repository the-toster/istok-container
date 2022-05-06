<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

class StaticResolver implements ModelResolver
{
    public function __construct(
        private readonly string $typeName,
        private readonly mixed $instance,
    ) {
    }

    public function match(string $type): bool
    {
        return $this->typeName === $type;
    }

    public function resolve(string $type): mixed
    {
        return $this->instance;
    }

}
