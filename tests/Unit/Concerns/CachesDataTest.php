<?php

declare(strict_types = 1);

use Centrex\TallUi\Concerns\CachesData;
use Illuminate\Support\Facades\Cache;

// Minimal host that uses the trait without Livewire overhead
class CacheHost
{
    use CachesData;

    public int $cacheTtl = 0;

    public ?string $cacheStore = null;

    protected function componentCacheTag(): string
    {
        return 'tallui:Tests.CacheHost';
    }
}

describe('CachesData::cacheKey()', function (): void {
    it('namespaces the key with tallui:', function (): void {
        $host = new CacheHost();
        $key  = $host->cacheKey('datatable', 'abc123');

        expect($key)->toBe('tallui:datatable:abc123');
    });

    it('filters empty parts', function (): void {
        $host = new CacheHost();
        $key  = $host->cacheKey('datatable', '', 'xyz');

        expect($key)->toBe('tallui:datatable:xyz');
    });
});

describe('CachesData::rememberCache()', function (): void {
    it('executes callback when cacheTtl is 0', function (): void {
        $host    = new CacheHost();
        $invoked = 0;

        $host->rememberCache('key', function () use (&$invoked): string {
            $invoked++;

            return 'value';
        });

        // Called every time — no caching
        $host->rememberCache('key', function () use (&$invoked): string {
            $invoked++;

            return 'value';
        });

        expect($invoked)->toBe(2);
    });

    it('caches the callback result when cacheTtl > 0', function (): void {
        $host          = new CacheHost();
        $host->cacheTtl = 60;
        $invoked       = 0;

        $result1 = $host->rememberCache('cache-test-key', function () use (&$invoked): string {
            $invoked++;

            return 'cached-value';
        });

        $result2 = $host->rememberCache('cache-test-key', function () use (&$invoked): string {
            $invoked++;

            return 'cached-value';
        });

        expect($result1)->toBe('cached-value')
            ->and($result2)->toBe('cached-value')
            ->and($invoked)->toBe(1); // callback only called once
    });
});

describe('CachesData::forgetCache()', function (): void {
    it('removes a cached key', function (): void {
        $host          = new CacheHost();
        $host->cacheTtl = 60;

        $host->rememberCache('forget-me', fn (): string => 'original');

        $host->forgetCache('forget-me');

        $invoked = 0;
        $host->rememberCache('forget-me', function () use (&$invoked): string {
            $invoked++;

            return 'refreshed';
        });

        expect($invoked)->toBe(1); // callback fired again after forget
    });
});

describe('CachesData::invalidateCache()', function (): void {
    it('clears all keys registered under the component tag', function (): void {
        $host          = new CacheHost();
        $host->cacheTtl = 60;

        $host->rememberCacheTracked('tag-key-1', fn (): string => 'v1');
        $host->rememberCacheTracked('tag-key-2', fn (): string => 'v2');

        $host->invalidateCache();

        $callCount = 0;

        $host->rememberCacheTracked('tag-key-1', function () use (&$callCount): string {
            $callCount++;

            return 'fresh';
        });

        expect($callCount)->toBe(1); // key was invalidated, callback re-ran
    });
});
