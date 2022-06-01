<?php

declare(strict_types=1);

namespace Test\ModelResolver\Fixture;


final class WithScalars
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly float $val,
        public readonly bool $flag,
    )
    {
    }
}
