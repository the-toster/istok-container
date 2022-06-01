<?php

declare(strict_types=1);

namespace Istok\Container;

interface Resolver
{
    /** @param class-string $type */
    public function resolve(string $type, array $arguments): mixed;
}
