<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


use Istok\Container\NotResolvable;

final class ConstructorResolver
{
    public function resolve(\ReflectionClass $class, array $arguments): object
    {
        $params = $class->getConstructor()->getParameters();
        $args = [];
        foreach ($params as $parameter) {
            $args[$parameter->getName()] = $this->resolveParameter($parameter, $arguments);
        }
    }

    private function resolveParameter(\ReflectionParameter $parameter, array $arguments): mixed
    {
        $name = $parameter->getName();

        if (!array_key_exists($name, $arguments)) {
            return null;
        }

        $type = $parameter->getType();
        if (is_null($type)) {
            return $arguments[$name];
        }

        if (!($type instanceof \ReflectionNamedType)) {
            throw new NotResolvable('Intersection and union types are not supported');
        }

        if ($parameter->isVariadic()) {
            return $this->handleArray($type, $arguments[$name]);
        }

        if (!$type->isBuiltin()) {
            return $this->coerce($type, $arguments[$name]);
        }

        return $this->tryResolveRecursive($type->getName(), $arguments[$name]);
    }


    private function tryResolveRecursive(string $typeName, mixed $arguments): mixed
    {
        if (enum_exists($typeName)) {
            return $this->resolveEnum($typeName, $arguments);
        }

        if (!class_exists($typeName)) {
            throw new NotResolvable('Non-class type not supported');
        }


        return $this->resolve(new \ReflectionClass($typeName), $arguments);
    }

    private function coerce(\ReflectionNamedType $to, mixed $v): mixed
    {
        return match ($to->getName()) {
            'array', 'iterable' => (array)$v,
            'object' => (object)$v,
            'bool' => (bool)$v,
            'float' => (float)$v,
            'int' => (int)$v,
            'string', 'callable' => (string)$v,
            default => $v
        };
    }

    private function resolveEnum(string $typeName, mixed $argument): mixed
    {
        if (!is_scalar($argument)) {
            throw new NotResolvable('Can\'t resolve enum from non-scalar value');
        }

        $case = (string)$argument;

        $reflection = new \ReflectionEnum($typeName);

        if ($reflection->hasCase($case)) {
            return $reflection->getCase($case)->getValue();
        }

        if ($reflection->isBacked()) {
            foreach ($reflection->getCases() as $enumCase) {
                if ((string)$enumCase->getBackingValue() === $case) {
                    return $enumCase->getValue();
                }
            }
        }

        throw new NotResolvable("Enum doesn't contain such case");
    }

    private function handleArray(\ReflectionNamedType $type, array $values): array
    {
        $r = [];
        foreach ($values as $v) {
            $r = $this->tryResolveRecursive($type->getName(), $v);
        }

        return $r;
    }
}
