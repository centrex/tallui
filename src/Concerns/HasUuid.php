<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Concerns;

trait HasUuid
{
    public string $uuid;

    protected function generateUuid(mixed ...$extra): void
    {
        $this->uuid = 'tallui-' . md5(serialize([$this, ...$extra]));
    }
}
