<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Support;

use Illuminate\Support\HtmlString;

class PackageVite
{
    /**
     * @param  array<int, string>  $entries
     */
    public static function render(string $basePath, string $hotFile, array $entries): HtmlString
    {
        $hotPath = rtrim($basePath, DIRECTORY_SEPARATOR) . '/public/' . ltrim($hotFile, '/');

        if (is_file($hotPath)) {
            $devServer = rtrim(trim((string) file_get_contents($hotPath)), '/');
            $tags = ['<script type="module" src="' . e($devServer . '/@vite/client') . '"></script>'];

            foreach ($entries as $entry) {
                $tags[] = '<script type="module" src="' . e($devServer . '/' . ltrim($entry, '/')) . '"></script>';
            }

            return new HtmlString(implode("\n", $tags));
        }

        $manifestPath = self::manifestPath($basePath);

        if (!$manifestPath) {
            return new HtmlString('');
        }

        /** @var array<string, array<string, mixed>> $manifest */
        $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        $assetBase = self::assetBase($basePath, dirname($manifestPath));
        $tags = [];

        foreach ($entries as $entry) {
            $chunk = $manifest[$entry] ?? null;

            if (!is_array($chunk)) {
                continue;
            }

            foreach (($chunk['css'] ?? []) as $cssFile) {
                $tags[] = '<link rel="stylesheet" href="' . e($assetBase . '/' . ltrim((string) $cssFile, '/')) . '">';
            }

            if (isset($chunk['file'])) {
                $tags[] = '<script type="module" src="' . e($assetBase . '/' . ltrim((string) $chunk['file'], '/')) . '"></script>';
            }
        }

        return new HtmlString(implode("\n", array_unique($tags)));
    }

    private static function manifestPath(string $basePath): ?string
    {
        $candidates = [
            rtrim($basePath, DIRECTORY_SEPARATOR) . '/public/build/manifest.json',
            rtrim($basePath, DIRECTORY_SEPARATOR) . '/public/build/.vite/manifest.json',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private static function assetBase(string $basePath, string $manifestDirectory): string
    {
        $publicPath = rtrim($basePath, DIRECTORY_SEPARATOR) . '/public';
        $relativePath = trim(str_replace($publicPath, '', $manifestDirectory), DIRECTORY_SEPARATOR);

        return $relativePath === '' ? asset('') : asset($relativePath);
    }
}
