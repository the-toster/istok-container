<?php

declare(strict_types=1);

namespace Istok\Container\Psr;


use Istok\Container\Container;
use Istok\Container\NotResolvable;
use Psr\Container\ContainerInterface;

final class GreedyContainer implements ContainerInterface
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
        try {
            $this->container->make($id);
        } catch (NotResolvable|NotFound) {
            return false;
        }
        return true;
    }

    public function singleton(string $id, string|\Closure $def): void
    {
        $this->container->singleton($id, $def);
    }
}
