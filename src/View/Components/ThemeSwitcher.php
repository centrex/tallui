<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ThemeSwitcher extends Component
{
    /** @var array<int, array{name: string, label: string, mode: string}> */
    public array $normalizedThemes;

    public string $default;

    /**
     * @param  array<int, array{name?: string, label?: string, mode?: string}>  $themes  Defaults to config('tallui.theme.options')
     */
    public function __construct(
        public array $themes = [],
        ?string $default = null,
        public string $position = 'bottom-end', // bottom-end | bottom-start
        public bool $withLabel = true,
    ) {
        $options = $themes !== [] ? $themes : (array) config('tallui.theme.options', [
            ['name' => 'light', 'label' => 'Light', 'mode' => 'light'],
            ['name' => 'dark', 'label' => 'Dark', 'mode' => 'dark'],
        ]);

        $this->normalizedThemes = collect($options)
            ->map(static function (array $option): array {
                $name = (string) ($option['name'] ?? 'light');

                return [
                    'name'  => $name,
                    'label' => (string) ($option['label'] ?? ucfirst($name)),
                    'mode'  => ($option['mode'] ?? 'light') === 'dark' ? 'dark' : 'light',
                ];
            })
            ->values()
            ->all();

        $this->default = $default ?? (string) config('tallui.theme.default', 'light');
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    open: false,
                    theme: @js($default),
                    options: @js($normalizedThemes),
                    currentOption() {
                        return this.options.find((option) => option.name === this.theme) ?? this.options[0];
                    },
                    applyTheme() {
                        document.documentElement.setAttribute('data-theme', this.theme);
                        document.documentElement.classList.toggle('dark', this.currentOption()?.mode === 'dark');
                    },
                    select(name) {
                        this.theme = name;
                        localStorage.setItem('theme', this.theme);
                        localStorage.setItem('theme-mode', this.currentOption()?.mode ?? 'light');
                        this.applyTheme();
                        this.open = false;
                        window.dispatchEvent(new CustomEvent('tallui-theme-changed', {
                            detail: { theme: this.theme, mode: this.currentOption()?.mode ?? 'light' },
                        }));
                    },
                    init() {
                        this.theme = localStorage.getItem('theme') || this.theme;
                        this.applyTheme();
                    },
                }"
                x-init="init()"
                class="relative inline-block"
                {{ $attributes }}
            >
                <button
                    type="button"
                    @click="open = !open"
                    class="btn btn-sm btn-ghost gap-2"
                    aria-haspopup="listbox"
                    :aria-expanded="open"
                >
                    @if($withLabel)
                        <span x-text="currentOption()?.label"></span>
                    @endif
                    <x-tallui-icon name="o-chevron-down" class="w-4 h-4" />
                </button>

                <ul
                    x-show="open"
                    x-transition
                    @click.outside="open = false"
                    @keydown.escape.window="open = false"
                    role="listbox"
                    class="{{ $position === 'bottom-start' ? 'left-0' : 'right-0' }} absolute z-50 mt-1 w-44 max-h-72 overflow-y-auto rounded-box border border-base-300 bg-base-100 py-1 shadow-lg"
                    style="display:none"
                >
                    <template x-for="option in options" :key="option.name">
                        <li role="option" :aria-selected="theme === option.name">
                            <button
                                type="button"
                                @click="select(option.name)"
                                class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm hover:bg-base-200"
                                :class="{ 'font-semibold': theme === option.name }"
                            >
                                <span x-text="option.label"></span>
                                <x-tallui-icon name="o-check" class="w-4 h-4" x-show="theme === option.name" />
                            </button>
                        </li>
                    </template>
                </ul>
            </div>
            BLADE;
    }
}
