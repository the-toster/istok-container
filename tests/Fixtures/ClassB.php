<?php

declare(strict_types=1);

namespace Test\Fixtures;


final class ClassB
{

    public function __construct(public readonly ClassA $classA)
    {
    }
}
