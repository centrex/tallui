<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Concerns;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

trait CachesData
{
    /**
     * Cache TTL in seconds. 0 = caching disabled.
     * Set on the component: public int $cacheTtl = 300;
     */
    public int $cacheTtl = 0;

    /**
     * Cache store to use. Null = default store from config/cache.php.
     * Override with e.g. 'redis' for better performance.
     */
    public ?string $cacheStore = null;

    /**
     * Build a namespaced cache key from the given parts.
     */
    protected function cacheKey(string ...$parts): string
    {
        return 'tallui:' . implode(':', array_filter($parts));
    }

    /**
     * Execute $callback, caching the result when $cacheTtl > 0.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    protected function rememberCache(string $key, callable $callback): mixed
    {
        if ($this->cacheTtl <= 0) {
            return $callback();
        }

        $store = Cache::store($this->cacheStore);

        return $store->remember($key, $this->cacheTtl, $callback);
    }

    /**
     * Forget a specific cache key.
     */
    public function forgetCache(string $key): void
    {
        Cache::store($this->cacheStore)->forget($key);
    }

    /**
     * Invalidate all cache entries for this component class.
     * Uses cache tags when the store supports them (Redis/Memcached),
     * otherwise tracks keys in a registry stored in cache.
     */
    public function invalidateCache(): void
    {
        $store = Cache::store($this->cacheStore);

        // Tag-based invalidation (Redis, Memcached)
        if ($store->getStore() instanceof TaggableStore) {
            $store->tags([$this->componentCacheTag()])->flush();

            return;
        }

        // Array store is per-request memory — flush wipes all entries instantly,
        // no registry needed.
        Cache::store('array')->flush();
    }

    /**
     * Like rememberCache() but also registers the key for tag-less invalidation.
     *
     * @template T
     *
     * @param  callable(): T  $callback
     * @return T
     */
    protected function rememberCacheTracked(string $key, callable $callback): mixed
    {
        if ($this->cacheTtl <= 0) {
            return $callback();
        }

        $store = Cache::store($this->cacheStore);

        // Tag-based stores handle invalidation automatically
        if ($store->getStore() instanceof TaggableStore) {
            return $store->tags([$this->componentCacheTag()])->remember($key, $this->cacheTtl, $callback);
        }

        // Non-taggable driver: use the array store (per-request memory).
        // No registry needed — invalidateCache() calls flush() on the array store.
        return Cache::store('array')->remember($key, $this->cacheTtl, $callback);
    }

    /**
     * A stable tag/prefix representing this component class.
     */
    protected function componentCacheTag(): string
    {
        return 'tallui:' . str_replace('\\', '.', static::class);
    }
}
