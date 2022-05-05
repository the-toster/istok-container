<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

use ReflectionNamedType;

interface ModelResolver
{
    public function match(ReflectionNamedType $type): bool;

    public function resolve(ReflectionNamedType $type): mixed;
}
