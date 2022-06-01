<?php

declare(strict_types=1);

namespace Istok\Container;

use Closure;
use Istok\Container\Psr\NotFound;
use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

final class Container
{
    /** @var array<string,Closure|string> */
    private array $items = [];

    /** @var array<string,array<string,Closure>> */
    private array $params = [];

    private Cache $cache;

    public function __construct()
    {
        $this->cache = new Cache();
    }

    public function make(string $id): mixed
    {

        if ($this->cache->has($id)) {
            return $this->cache->get($id);
        }

        if (!$this->has($id) && !class_exists($id)) {
            throw new NotFound();
        }

        $entity = $this->items[$id] ?? $id;

        if ($entity instanceof Closure) {
            /** @psalm-suppress MixedAssignment */
            $r = $this->call($entity);
            $this->cache->cacheIfShould($id, $r);
            return $r;
        }

        if (class_exists($entity)) {
            /** @psalm-suppress MixedAssignment */
            $r = $this->build($entity);
            $this->cache->cacheIfShould($id, $r);
            return $r;
        }

        throw new NotResolvable($id);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->items);
    }

    /** @param class-string $id */
    private function build(string $id): mixed
    {
        $reflection = new ReflectionClass($id);

        foreach (
            $reflection->getAttributes(
                Resolver::class,
                \ReflectionAttribute::IS_INSTANCEOF
            ) as $resolverAttribute
        ) {
            $resolverName = $resolverAttribute->getName();
            if ($this->has($resolverName)) {
                /** @var Resolver $resolver */
                $resolver = $this->make($resolverName);
                return $resolver->resolve($id, $resolverAttribute->getArguments());
            }
        }

        $constructor = $reflection->getConstructor();
        if ($constructor && !$constructor->isPublic()) {
            throw new NotResolvable();
        }

        $args = $this->resolveArguments($constructor?->getParameters() ?? [], $id);

        /** @psalm-suppress MixedMethodCall */
        return new $id(...$args);
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @return T
     * @psalm-suppress MixedInferredReturnType
     */
    public function construct(string $id): object
    {
        /** @psalm-suppress MixedAssignment */
        $instance = $this->make($id);
        if ($instance instanceof $id) {
            throw new NotResolvable('Resolved object not an instance of requested class');
        }
        /** @psalm-suppress MixedReturnStatement */
        return $instance;
    }

    /** @param array<string,mixed> $arguments */
    public function call(Closure $closure, array $arguments = []): mixed
    {
        $reflection = new ReflectionFunction($closure);

        $givenArguments = [];
        $toResolve = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            if (array_key_exists($name, $arguments)) {
                /** @psalm-suppress MixedAssignment */
                $givenArguments[$name] = $arguments[$name];
                continue;
            }

            $toResolve[] = $parameter;
        }

        return $closure(...$givenArguments, ...$this->resolveArguments($toResolve, null));
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
                $arguments[] = $this->make((string)$type);
                continue;
            }

            throw new NotResolvable();
        }

        return $arguments;
    }

    public function singleton(string $id, Closure|string $def): void
    {
        $this->register($id, $def);
        $this->cache->shouldCache($id);
    }

    public function register(string $id, \Closure|string $def): void
    {
        $this->cache->shouldNotCache($id);
        $this->cache->reset($id);
        $this->items[$id] = $def;
    }

    public function argument(string $name, string $for, Closure $resolver): void
    {
        $this->params[$for][$name] = $resolver;
    }

}
