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

describe('HasUuid trait', function (): void {
    it('generates a uuid string', function (): void {
        $host = new UuidHost();

        expect($host->uuid)->toBeString()->not->toBeEmpty();
    });

    it('uuid starts with tallui- prefix', function (): void {
        $host = new UuidHost();

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
});
