<?php

declare(strict_types=1);

namespace Test\Fixtures;


final class WithMarker
{
    public function __construct(public readonly string $marker)
    {
    }
}
