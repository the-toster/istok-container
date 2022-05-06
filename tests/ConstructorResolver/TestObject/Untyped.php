<?php

declare(strict_types=1);

namespace Test\ConstructorResolver\TestObject;


final class Untyped
{

    public function __construct(
        public $a,
        public $b,
    )
    {
    }
}
