<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Tags extends Component
{
    public function __construct(
        public string  $name        = 'tags',
        public ?string $label       = null,
        public array   $value       = [],
        public ?string $placeholder = 'Add tag…',
        public ?string $helper      = null,
        public ?string $error       = null,
        public string  $color       = 'primary',
    ) {}

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    tags: {{ Js::from($value) }},
                    input: '',
                    add() {
                        const tag = this.input.trim();
                        if (tag && !this.tags.includes(tag)) { this.tags.push(tag); }
                        this.input = '';
                    },
                    remove(i) { this.tags.splice(i, 1); },
                    onKey(e)  { if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); this.add(); } },
                }"
                class="form-control w-full"
                {{ $attributes }}
            >
                @if($label)
                    <label class="label">
                        <span class="label-text font-medium">{{ $label }}</span>
                    </label>
                @endif

                {{-- Hidden input for form submission --}}
                <template x-for="(tag, i) in tags" :key="i">
                    <input type="hidden" name="{{ $name }}[]" :value="tag" />
                </template>

                {{-- Tag display + input --}}
                <div @class([
                    'input input-bordered flex flex-wrap items-center gap-1.5 min-h-[2.5rem] h-auto py-1.5 px-2',
                    'input-error' => $error,
                ])>
                    <template x-for="(tag, i) in tags" :key="i">
                        <span @class(['badge gap-1', "badge-{$color}"])>
                            <span x-text="tag"></span>
                            <button type="button" @click="remove(i)" class="hover:opacity-70">&times;</button>
                        </span>
                    </template>
                    <input
                        x-model="input"
                        @keydown="onKey($event)"
                        @blur="add()"
                        type="text"
                        placeholder="{{ $placeholder }}"
                        class="flex-1 min-w-[80px] bg-transparent outline-none border-none text-sm p-0"
                    />
                </div>

                @if($error)
                    <label class="label"><span class="label-text-alt text-error">{{ $error }}</span></label>
                @elseif($helper)
                    <label class="label"><span class="label-text-alt text-base-content/60">{{ $helper }}</span></label>
                @endif
            </div>
            BLADE;
    }
}
