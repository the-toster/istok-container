<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Source
{
    public function __construct(public readonly string $name)
    {
    }
}
