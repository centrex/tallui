<?php

declare(strict_types = 1);

use Centrex\TallUi\Concerns\HasUuid;

// Minimal class that uses the trait
class UuidHost
{
    use HasUuid;

    public function __construct(public string $name = 'test')
    {
        $this->generateUuid($this->name);
    }
}

// Mirrors how Button/Dialog/Choices/Pin/FileUpload call generateUuid(): with an
// optional, often-null $id — the path that previously collided via serialize($this).
class UuidHostNoExplicitId
{
    use HasUuid;

    public function __construct(public ?string $id = null, public ?string $label = null)
    {
        $this->generateUuid($this->id);
    }
}

describe('HasUuid trait', function (): void {
    it('generates a uuid string', function (): void {
        $host = new UuidHost;

        expect($host->uuid)->toBeString()->not->toBeEmpty();
    });

    it('uuid starts with tallui- prefix', function (): void {
        $host = new UuidHost;

        expect($host->uuid)->toStartWith('tallui-');
    });

    it('produces the same uuid for the same input', function (): void {
        $a = new UuidHost('alpha');
        $b = new UuidHost('alpha');

        expect($a->uuid)->toBe($b->uuid);
    });

    it('produces different uuids for different inputs', function (): void {
        $a = new UuidHost('alpha');
        $b = new UuidHost('beta');

        expect($a->uuid)->not->toBe($b->uuid);
    });

    it('does not collide for sibling instances with identical props and no explicit id', function (): void {
        // Regression: generateUuid() used to hash serialize($this), so two instances
        // with the same constructor props (e.g. two default "Edit" buttons in a loop)
        // produced the exact same uuid, causing duplicate wire:key / dialog id clashes.
        $a = new UuidHostNoExplicitId(label: 'Edit');
        $b = new UuidHostNoExplicitId(label: 'Edit');

        expect($a->uuid)->not->toBe($b->uuid);
    });

    it('uses the explicit id verbatim when one is provided', function (): void {
        $host = new UuidHostNoExplicitId(id: 'row-42');

        expect($host->uuid)->toBe('tallui-row-42');
    });
});
