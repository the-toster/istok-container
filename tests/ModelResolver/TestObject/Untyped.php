<?php

declare(strict_types=1);

namespace Test\ModelResolver\TestObject;


final class Untyped
{

    public function __construct(
        public $a,
        public $b,
    )
    {
    }
}
