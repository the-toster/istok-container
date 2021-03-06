<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


final class Constructor
{
    /** @param array<string,mixed> $input */
    public function resolve(\ReflectionClass $class, array $input): object
    {
        $params = $class->getConstructor()?->getParameters() ?? [];
        $args = [];
        $variadic = [];
        foreach ($params as $parameter) {
            $name = $parameter->getName();

            if (!array_key_exists($name, $input)) {
                $args[$name] = null;
                continue;
            }

            try {
                /** @psalm-suppress MixedAssignment */
                if ($parameter->isVariadic()) {
                    foreach ($input[$name] as $inputItem) {
                        $variadic[] = $this->resolveParameter($parameter, $inputItem);
                    }
                } else {
                    $args[$name] = $this->resolveParameter($parameter, $input[$name]);
                }
            } catch (\Throwable $e) {
                throw ModelResolutionException::for($class->name, $name, $e);
            }
        }
        /** @psalm-suppress InvalidStringClass */
        return new $class->name(...$args, ...$variadic);
    }

    private function resolveParameter(\ReflectionParameter $parameter, mixed $input): mixed
    {
        $type = $parameter->getType();

        if (is_null($type)) {
            return $input;
        }

        if (!($type instanceof \ReflectionNamedType)) {
            throw ParameterResolutionException::nonNamedType($type);
        }

        return $this->resolveNamedType($type, $input);
    }

    private function resolveNamedType(\ReflectionNamedType $type, mixed $argument): mixed
    {
        if ($type->isBuiltin()) {
            return $this->coerce($type, $argument);
        }

        $name = $type->getName();
        if (enum_exists($name)) {
            return $this->resolveEnum($name, $argument);
        }

        if (class_exists($name)) {
            if (!is_array($argument)) {
                throw ParameterResolutionException::missedArgumentsArray();
            }
            /** @psalm-suppress MixedArgumentTypeCoercion */
            return $this->resolve(new \ReflectionClass($name), $argument);
        }

        /** @psalm-suppress MixedArgument */
        throw ParameterResolutionException::unsupportedParameterType($name);
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
        if (!is_string($argument) && !is_int($argument)) {
            throw ParameterResolutionException::invalidEnumValueType($argument);
        }

        $case = (string)$argument;

        $reflection = new \ReflectionEnum($typeName);

        if ($reflection->hasCase($case)) {
            return $reflection->getCase($case)->getValue();
        }

        if ($reflection->isBacked()) {
            /** @var \ReflectionEnumBackedCase $enumCase */
            foreach ($reflection->getCases() as $enumCase) {
                if ((string)$enumCase->getBackingValue() === $case) {
                    return $enumCase->getValue();
                }
            }
        }

        throw ParameterResolutionException::noSuchCase($case);
    }

}
