<?php

declare(strict_types=1);

namespace Test\ModelResolver\Fixture;


final class Item
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
    )
    {
    }
}
