<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


final class ParameterResolutionException extends \InvalidArgumentException
{
    public static function nonNamedType(\ReflectionType $type): self
    {
        return new self("Intersection and union types are not supported: ".get_debug_type($type));
    }

    public static function missedArgumentsArray(): self
    {
        return new self("Array of arguments not provided.");
    }

    public static function unsupportedParameterType(string $typeName): self
    {
        return new self("Class (only concrete types supported) not found: $typeName");
    }

    public static function invalidEnumValueType(mixed $value): self
    {
        return new self("Enum can be constructed only from string or int, " . get_debug_type($value) . " provided");
    }

    public static function noSuchCase(string $argument): self
    {
        return new self("Enum doesn't contain such case: " . $argument);
    }
}
