<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


use Istok\Container\NotResolvable;

final class CompositeModelResolver implements ModelResolver
{
    private readonly array $resolvers;

    public function __construct(ModelResolver ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    private function find(string $type): ?ModelResolver
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->match($type)) {
                return $resolver;
            }
        }

        return null;
    }

    public function match(string $type): bool
    {
        return !is_null($this->find($type));
    }

    public function resolve(string $type): mixed
    {
        $resolver = $this->find($type);
        if (!$resolver) {
            throw new NotResolvable();
        }
        return $resolver->resolve($type);
    }

}
