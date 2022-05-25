<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


final class ModelResolver implements Resolver
{
    public function __construct(
        private readonly array $data
    ) {
    }

    public function resolve(string $type, array $arguments = []): mixed
    {
        $class = new \ReflectionClass($type);
        return (new Constructor())->resolve($class, $this->data);
    }


}
