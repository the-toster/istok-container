<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

use ReflectionNamedType;

interface ModelResolver
{
    public function match(string $type): bool;

    public function resolve(string $type): mixed;
}
