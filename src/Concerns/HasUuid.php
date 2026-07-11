<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Concerns;

trait HasUuid
{
    public string $uuid;

    protected function generateUuid(mixed ...$extra): void
    {
        $explicit = array_filter($extra, static fn (mixed $value): bool => is_string($value) && $value !== '');

        // An explicit id is a stable, developer-chosen key (e.g. per-row buttons/dialogs).
        // Without one, fall back to a per-instance unique value instead of hashing the
        // component's props — identical sibling instances would otherwise collide.
        $this->uuid = $explicit !== []
            ? 'tallui-' . implode('-', $explicit)
            : 'tallui-' . str_replace('.', '', uniqid('', true));
    }
}
