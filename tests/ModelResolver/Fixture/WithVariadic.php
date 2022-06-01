<?php

declare(strict_types=1);

namespace Test\ModelResolver\Fixture;


final class WithVariadic
{
    public readonly array $items;

    public function __construct(public readonly string $a, Item ...$items)
    {
        $this->items = $items;
    }
}
