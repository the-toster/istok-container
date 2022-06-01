<?php

declare(strict_types=1);

namespace Test\ModelResolver\Fixture;


final class WithArray
{
    public function __construct(
        public readonly array $a1,
        public readonly iterable $a2,
    ) {
    }
}
