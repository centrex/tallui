<?php

declare(strict_types = 1);

use Centrex\TallUi\View\Components\{Card, Progress};

describe('DaisyUI 5 class regressions', function (): void {
    it('Card no longer emits dead DaisyUI 3/4 class names', function (): void {
        $rendered = (new Card(bordered: true, padding: 'compact'))->render();

        expect($rendered)->not->toContain('card-bordered')
            ->and($rendered)->not->toContain('card-compact')
            ->and($rendered)->not->toContain('card-normal')
            ->and($rendered)->toContain('card-border')
            ->and($rendered)->toContain('card-xs')
            ->and($rendered)->toContain('card-md');
    });

    it('Progress no longer emits the dead progress-{size} class', function (): void {
        $rendered = (new Progress(size: 'lg'))->render();

        expect($rendered)->not->toContain('progress-{$size}')
            ->and($rendered)->toContain("'lg' => 'h-3'");
    });
});
