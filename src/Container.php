<?php

declare(strict_types=1);

namespace Istok\Container;

use Closure;
use Istok\Container\ContainerInterface as IstokContainer;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

final class Container implements ContainerInterface, IstokContainer
{
    /** @var array<string,Closure|string> */
    private array $items = [];

    /** @var array<string,array<string,Closure>> */
    private array $params = [];


    public function has(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id) && !class_exists($id)) {
            throw new NotFound();
        }

        $entity = $this->items[$id] ?? $id;

        if ($entity instanceof Closure) {
            return $this->call($entity);
        }

        if (is_string($entity) && class_exists($entity)) {
            return $this->build($entity);
        }

        throw new NotResolvable($id);
    }

    private function build(string $id): mixed
    {
        $reflection = new ReflectionClass($id);

        foreach($reflection->getAttributes(Resolver::class, \ReflectionAttribute::IS_INSTANCEOF) as $resolverAttribute) {
            $resolverName = $resolverAttribute->getName();
            if($this->has($resolverName)) {
                /** @var Resolver $resolver */
                $resolver = $this->get($resolverName);
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
     */
    public function construct(string $id): object
    {
        $instance = $this->build($id);
        if ($instance instanceof $id) {
            throw new NotResolvable('Resolved object not an instance of requested class');
        }

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
                $arguments[] = $this->get((string)$type);
                continue;
            }

            throw new NotResolvable();
        }

        return $arguments;
    }

    public function set(string $id, Closure|string $def): void
    {
        $this->items[$id] = $def;
    }

    public function bindArgument(string $name, string $for, Closure $resolver): void
    {
        $this->params[$for][$name] = $resolver;
    }
}
