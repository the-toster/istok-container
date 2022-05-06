<?php

declare(strict_types=1);

namespace Test\ConstructorResolver\TestObject;


final class Rich
{
    public readonly array $items;

    public function __construct(
        public readonly string $id,
        public readonly int $n,
        public readonly bool $flag,
        public readonly array $a1,
        public readonly iterable $a2,
        public readonly Item $singleItem,
        Item ...$items,
    )
    {
        $this->items = $items;
    }
}
