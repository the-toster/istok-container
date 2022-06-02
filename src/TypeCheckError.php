<?php

declare(strict_types=1);

namespace Istok\Container;


final class TypeCheckError extends \TypeError
{
    public static function requestedUnknown(string $requested): self
    {
        return new self("Request instance of unknown type [$requested]");
    }

    public static function resolvedNonInstance(mixed $resolved, string $requested): self
    {
        $type = get_debug_type($resolved);
        return new self("Resolved value ($type) not an instance of requested type [$requested]");
    }
}
