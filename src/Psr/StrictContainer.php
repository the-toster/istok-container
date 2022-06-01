<?php

declare(strict_types=1);

namespace Istok\Container\Psr;


use Istok\Container\Container;
use Psr\Container\ContainerInterface;

final class StrictContainer implements ContainerInterface
{

    public function __construct(private readonly Container $container)
    {
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new NotFound();
        }

        return $this->container->make($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    public function singleton(string $id, string|\Closure $def): void
    {
        $this->container->singleton($id, $def);
    }
}
