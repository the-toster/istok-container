<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

class StaticResolver implements Resolver
{
    public function __construct(
        private readonly mixed $instance,
    ) {
    }

    public function resolve(string $type, array $arguments = []): mixed
    {
        return $this->instance;
    }

}
