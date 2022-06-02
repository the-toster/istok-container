<?php

declare(strict_types=1);

namespace Istok\Container;


final class ResolutionException extends \InvalidArgumentException
{

    public static function unknownEntry(string $id): self
    {
        return new self("Given entry [$id] is not registered and not a known class name");
    }

    public static function nonPublicConstructor(string $id): self
    {
        return new self("Auto-resolution of [$id] failed, constructor is not public");
    }

    public static function unknownParameter(string $name, ?string $forId): self
    {
        $message = "Failed to resolve \"$name\" parameter";

        if ($forId) {
            $message = "Failed to resolve \"$name\" parameter for [$forId]";
        }

        return new self($message);
    }
}
