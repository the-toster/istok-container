<?php

declare(strict_types=1);

namespace Test\ModelResolver\Fixture;

final class WithEnums
{
    public function __construct(public BackedEnum $backed, public PureEnum $pure)
    {
    }
}
