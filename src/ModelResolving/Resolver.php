<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

interface Resolver
{
    /** @param class-string $type */
    public function resolve(string $type, string $key): mixed;
}
