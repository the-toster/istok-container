<?php

declare(strict_types=1);

namespace TheToster\Container;

use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    private array $items = [];

    public function get(string $id): mixed
    {
        if (!$this->has($id) && !class_exists($id) && !interface_exists($id)) {
            throw new NotFound();
        }

        $entity = $this->items[$id] ?? $id;

        try {
            return $this->resolve($entity);
        } catch (\Throwable $e) {
            throw new NotResolvable($id, 0, $e);
        }
    }

    private function resolve(mixed $entity): mixed
    {
        if ($entity instanceof \Closure) {
            return $this->call($entity);
        }
    }

    public function call(\Closure $closure): mixed
    {
        $reflection = new \ReflectionFunction($closure);
        $arguments = [];
        foreach ($reflection->getParameters() as $parameter) {
            if ($parameter->isOptional()) {
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            $type = $parameter->getType();
            if ($type) {
                $arguments[] = $this->resolveType($type);
                continue;
            }

            $arguments[] = $this->resolveByName($parameter->name);
        }

        return $closure(...$arguments);
    }

    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function set(string $id, mixed $def): void
    {
        $this->items[$id] = $def;
    }

    private function resolveType(\ReflectionIntersectionType|\ReflectionUnionType|\ReflectionNamedType $type): mixed
    {
        if ($type->allowsNull()) {
            return null;
        }
        return $this->get((string)$type);
    }

    private function resolveByName(string $name)
    {
        throw new \RuntimeException('unimplemented');
    }
}
