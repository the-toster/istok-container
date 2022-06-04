<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


class ModelResolver implements Resolver
{
    /** @param array<string,mixed> $data */
    public function __construct(
        private readonly array $data = []
    ) {
    }

    /** @param class-string $type */
    public function resolve(string $type, string $key): mixed
    {
        $class = new \ReflectionClass($type);
        return (new Constructor())->resolve($class, $this->data);
    }


}
