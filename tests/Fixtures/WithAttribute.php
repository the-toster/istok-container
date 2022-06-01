<?php

declare(strict_types=1);

namespace Test\Fixtures;

#[TestResolver]
final class WithAttribute
{
    public function __construct(
        public readonly string $marker
    )
    {
    }
}
