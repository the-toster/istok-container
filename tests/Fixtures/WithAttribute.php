<?php

declare(strict_types=1);

namespace Test\Fixtures;

use Istok\Container\ModelResolving\ResolveBy;

#[ResolveBy(TestResolver::class)]
final class WithAttribute
{
    public function __construct(
        public readonly string $marker
    )
    {
    }
}
