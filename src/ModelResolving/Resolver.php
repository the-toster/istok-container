<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

interface Resolver
{
    public function resolve(string $type, array $arguments): mixed;
}
