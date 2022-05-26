<?php

declare(strict_types=1);

namespace Istok\Container;

interface Resolver
{
    public function resolve(string $type, array $arguments): mixed;
}
