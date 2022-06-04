<?php

declare(strict_types=1);

namespace Test\ModelResolver\Fixture;

final class Untyped
{

    /** @psalm-suppress MissingParamType */
    public function __construct(
        public $a,
        public $b,
    )
    {
    }
}
