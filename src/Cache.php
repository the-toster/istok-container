<?php

declare(strict_types=1);

namespace Istok\Container;


final class Cache
{

    /** @var array<string,bool> */
    private array $shouldCache = [];

    /** @var array<string,mixed> */
    private array $cache = [];

    public function reset(string $id): void
    {
        unset($this->cache[$id]);
    }

    public function cacheIfShould(string $id, mixed $val): void
    {
        if ($this->shouldCache[$id] ?? false) {
            $this->cache[$id] = $val;
        }
    }

    public function shouldCache(string $id): void
    {
        $this->shouldCache[$id] = true;
    }

    public function shouldNotCache(string $id): void
    {
        unset($this->shouldCache[$id]);
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new \OutOfRangeException('no such entry');
        }
        return $this->cache[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->cache);
    }
}
