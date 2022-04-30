<?php

declare(strict_types=1);

namespace TheToster\Container;

use Closure;
use Psr\Container\ContainerInterface;

use ReflectionClass;
use ReflectionFunction;
use ReflectionIntersectionType;

use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

use RuntimeException;

use Throwable;

use function Reflection;

final class Container implements ContainerInterface
{
    /** @var array<string,Closure|string> */
    private array $items = [];

    /** @var array<string,array<string,Closure>> */
    private array $params = [];

    public function get(string $id): mixed
    {
        if (!$this->has($id) && !class_exists($id)) {
            throw new NotFound();
        }

        $entity = $this->items[$id] ?? $id;

        try {
            return $this->resolve($entity);
        } catch (Throwable $e) {
            throw new NotResolvable($id, 0, $e);
        }
    }

    public function call(Closure $closure): mixed
    {
        $reflection = new ReflectionFunction($closure);

        $args = $this->resolveArguments($reflection->getParameters(), null);

        return $closure(...$args);
    }

    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function set(string $id, Closure|string $def): void
    {
        $this->items[$id] = $def;
    }

    public function bindArgument(string $name, string $for, Closure $resolver): void
    {
        $this->params[$for][$name] = $resolver;
    }

    private function resolve(mixed $entity): mixed
    {
        if ($entity instanceof Closure) {
            return $this->call($entity);
        }

        if (is_string($entity) && class_exists($entity)) {
            return $this->construct($entity);
        }

        throw new NotResolvable();
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function construct(string $className): object
    {
        $reflection = new ReflectionClass($className);
        $constructor = $reflection->getConstructor();

        if ($constructor && !$constructor->isPublic()) {
            throw new NotResolvable();
        }

        $args = $this->resolveArguments($constructor?->getParameters() ?? [], $className);

        /** @psalm-suppress MixedMethodCall */
        return new $className(...$args);
    }

    /** @param ReflectionParameter[] $parameters */
    private function resolveArguments(array $parameters, ?string $forId): array
    {
        $arguments = [];
        foreach ($parameters as $parameter) {
            $resolver = $this->params[$forId][$parameter->getName()] ?? null;
            if ($resolver) {
                /** @psalm-suppress MixedAssignment */
                $arguments[] = $this->call($resolver);
                continue;
            }

            if ($parameter->isOptional()) {
                /** @psalm-suppress MixedAssignment */
                $arguments[] = $parameter->getDefaultValue();
                continue;
            }

            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType) {
                /** @psalm-suppress MixedAssignment */
                $arguments[] = $this->get((string)$type);
                continue;
            }

            throw new NotResolvable();
        }

        return $arguments;
    }
}
