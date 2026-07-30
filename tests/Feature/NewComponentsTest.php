<?php

declare(strict_types = 1);

use Centrex\TallUi\View\Components\{ChatBubble, Divider, Dropdown, Kbd, Skeleton, Tooltip};

describe('Kbd', function (): void {
    it('renders the given key text', function (): void {
        expect((new Kbd(key: 'Ctrl'))->render())->toContain('{{ $key ?? $slot }}');
    });

    it('applies the size class', function (): void {
        expect((new Kbd(size: 'lg'))->render())->toContain('kbd-{$size}');
    });
});

describe('Divider', function (): void {
    it('is a separator with no accessible label when no text is given', function (): void {
        $rendered = (new Divider)->render();

        expect($rendered)->toContain('role="separator"')
            ->and($rendered)->toContain('aria-hidden="true"');
    });

    it('exposes an aria-label when a label is given', function (): void {
        expect((new Divider(label: 'OR'))->render())->toContain('aria-label="{{ $label }}"');
    });

    it('supports the vertical orientation', function (): void {
        expect((new Divider(vertical: true))->vertical)->toBeTrue();
    });
});

describe('Skeleton', function (): void {
    it('defaults to a single text bar', function (): void {
        $c = new Skeleton;

        expect($c->variant)->toBe('text')
            ->and($c->lines)->toBe(1);
    });

    it('renders a stack of bars for multi-line text skeletons', function (): void {
        $rendered = (new Skeleton(variant: 'text', lines: 3))->render();

        expect($rendered)->toContain('flex flex-col gap-2');
    });

    it('renders a circle skeleton', function (): void {
        expect((new Skeleton(variant: 'circle'))->render())->toContain('rounded-full');
    });
});

describe('Tooltip', function (): void {
    it('renders the DaisyUI native tooltip data-tip attribute', function (): void {
        $rendered = (new Tooltip(text: 'Save changes'))->render();

        expect($rendered)->toContain('data-tip="{{ $text }}"')
            ->and($rendered)->toContain('tooltip-{$position}');
    });
});

describe('ChatBubble', function (): void {
    it('defaults to the start position', function (): void {
        expect((new ChatBubble)->position)->toBe('start');
    });

    it('renders header when name or time is given', function (): void {
        $rendered = (new ChatBubble(name: 'Alice', time: '12:45'))->render();

        expect($rendered)->toContain('chat-header');
    });
});

describe('Dropdown', function (): void {
    it('normalizes flat item arrays with defaults', function (): void {
        $c = new Dropdown(items: [['label' => 'Edit']]);

        expect($c->normalizedItems[0])->toMatchArray([
            'label'     => 'Edit',
            'disabled'  => false,
            'separator' => false,
        ]);
    });

    it('renders the ARIA menu pattern', function (): void {
        $rendered = (new Dropdown(items: [['label' => 'Edit']]))->render();

        expect($rendered)->toContain('role="menu"')
            ->and($rendered)->toContain('role="menuitem"');
    });

    it('generates a unique id per instance', function (): void {
        $a = new Dropdown(items: []);
        $b = new Dropdown(items: []);

        expect($a->uuid)->not->toBe($b->uuid);
    });
});
