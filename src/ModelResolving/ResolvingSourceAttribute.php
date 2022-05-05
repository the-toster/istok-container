<?php

declare(strict_types=1);

namespace Istok\Container\ModelResolving;


use ReflectionNamedType;

final class ResolvingSourceAttribute implements ModelResolver
{
    public function __construct(
        private readonly string $sourceName,
        private readonly array $data
    ) {
    }

    private function findTargetSource(ReflectionNamedType $type): ?string
    {
        $name = $type->getName();
        if(!class_exists($name)) {
            return null;
        }
        $attributes = (new \ReflectionClass($name))->getAttributes(Source::class);

        foreach ($attributes as $attribute) {
            /** @var Source $instance */
            $instance = $attribute->newInstance();
            return $instance->name;
        }
        return null;
    }

    public function match(ReflectionNamedType $type): bool
    {
        return $this->findTargetSource($type) === $this->sourceName;
    }

    public function resolve(ReflectionNamedType $type): mixed
    {

    }


}
