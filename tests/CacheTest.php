<?php

declare(strict_types=1);

namespace Test;


use Istok\Container\Cache;
use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    /** @test */
    public function it_can_cache(): void
    {
        $v = '123';
        $k = 'A';

        $cache = new Cache();
        $cache->shouldCache($k);

        $this->assertFalse($cache->has($k));

        $cache->cacheIfShould($k, $v);

        $this->assertTrue($cache->has($k));
        $this->assertEquals($v, $cache->get($k));
    }

    /** @test */
    public function it_will_not_cache_without_directive(): void
    {
        $v = '123';
        $k = 'A';

        $cache = new Cache();
        $cache->cacheIfShould($k, $v);
        $this->assertFalse($cache->has($k));
    }

    /** @test */
    public function it_can_cancel_cache_directive(): void
    {
        $v = '123';
        $k = 'A';

        $cache = new Cache();
        $cache->shouldCache($k);
        $cache->cacheIfShould($k, $v);

        $cache->shouldNotCache($k);
        $cache->cacheIfShould($k, 'other');
        $this->assertEquals($v, $cache->get($k));
    }

    /** @test */
    public function it_can_reset_cached_entry(): void
    {
        $v = '123';
        $k = 'A';

        $cache = new Cache();
        $cache->shouldCache($k);
        $cache->cacheIfShould($k, $v);

        $cache->reset($k);
        $this->assertFalse($cache->has($k));
    }

    /** @test */
    public function it_supports_null_entry(): void
    {
        $v = '123';
        $k = 'A';

        $cache = new Cache();
        $cache->shouldCache($k);
        $cache->cacheIfShould($k, $v);
        $this->assertTrue($cache->has($k));
    }

    /** @test */
    public function it_can_rewrite_cache(): void
    {
        $v = '123';
        $k = 'A';

        $cache = new Cache();
        $cache->shouldCache($k);
        $cache->cacheIfShould($k, 'other');
        $cache->cacheIfShould($k, $v);

        $this->assertEquals($v, $cache->get($k));

    }

}
