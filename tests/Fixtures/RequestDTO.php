<?php

declare(strict_types=1);

namespace Test\Fixtures;

#[TestResolver]
final class RequestDTO
{
    public function __construct(
        public readonly string $marker
    )
    {
    }
}
