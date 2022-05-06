<?php

declare(strict_types=1);

namespace Test\ConstructorResolver\TestObject;


final class Item
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
    )
    {
    }
}
