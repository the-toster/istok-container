<?php

declare(strict_types=1);

namespace Test\ModelResolver\TestObject;

use Istok\Container\ModelResolving\Source;

#[Source('test')]
final class TestEnum
{
    public function __construct(public BackedEnum $backed, public PureEnum $pure)
    {
    }
}
