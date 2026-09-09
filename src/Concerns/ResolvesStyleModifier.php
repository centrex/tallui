<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Concerns;

trait ResolvesStyleModifier
{
    /**
     * Resolve the DaisyUI 5 style modifier ('soft' | 'outline' | 'dash' | 'solid'),
     * preferring an explicit per-instance value over the package-wide config default.
     */
    protected function resolveStyleModifier(?string $explicit): string
    {
        $style = $explicit ?? (string) config('tallui.style.default', 'solid');

        return in_array($style, ['soft', 'outline', 'dash'], true) ? $style : 'solid';
    }

    /** Build the "{prefix}-{modifier}" class for a resolved style, or null for 'solid'. */
    protected function styleModifierClass(string $prefix, string $style): ?string
    {
        return $style === 'solid' ? null : "{$prefix}-{$style}";
    }
}
