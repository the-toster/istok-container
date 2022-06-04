<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

#[\Attribute]
final class ResolveBy
{
    /**
     * @param class-string<Resolver> $resolverName
     */
    public function __construct(public readonly string $resolverName, public readonly string $key = '')
    {
    }
}
