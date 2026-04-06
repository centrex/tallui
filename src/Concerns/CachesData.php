<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Concerns;

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
        $tagKey = $this->componentCacheTag();

        // Tag-based invalidation (Redis, Memcached)
        if (method_exists($store, 'tags')) {
            $store->tags([$tagKey])->flush();

            return;
        }

        // Key-registry fallback: we maintain a list of keys under a registry entry
        $registryKey = 'tallui:registry:' . $tagKey;

        /** @var array<string> $keys */
        $keys = $store->get($registryKey, []);

        foreach ($keys as $cacheKey) {
            $store->forget($cacheKey);
        }

        $store->forget($registryKey);
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
        $tagKey = $this->componentCacheTag();

        // Tag-based stores handle invalidation automatically
        if (method_exists($store, 'tags')) {
            return $store->tags([$tagKey])->remember($key, $this->cacheTtl, $callback);
        }

        // Register the key in the registry for later invalidation
        $registryKey = 'tallui:registry:' . $tagKey;

        /** @var array<string> $keys */
        $keys = $store->get($registryKey, []);

        if (!in_array($key, $keys, true)) {
            $keys[] = $key;
            // Keep registry alive longer than the cached items
            $store->put($registryKey, $keys, $this->cacheTtl * 10);
        }

        return $store->remember($key, $this->cacheTtl, $callback);
    }

    /**
     * A stable tag/prefix representing this component class.
     */
    protected function componentCacheTag(): string
    {
        return 'tallui:' . str_replace('\\', '.', static::class);
    }
}
