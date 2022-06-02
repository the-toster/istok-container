<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


final class ModelResolutionException extends \InvalidArgumentException
{
    public static function for(string $id, string $parameterName, \Throwable $previous): self
    {
        return new self("Can't resolve '$parameterName' for $id: ".$previous->getMessage(), 0, $previous);
    }
}
