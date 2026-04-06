<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextEditor extends Component
{
    public string $editorId;

    public function __construct(
        public string  $name        = '',
        public ?string $label       = null,
        public ?string $placeholder = null,
        public ?string $value       = null,
        public ?string $error       = null,
        public ?string $helper      = null,
        public int     $rows        = 8,
        public bool    $required    = false,
    ) {
        $this->editorId = 'editor-' . ($name ?: uniqid());
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            <div
                x-data="{
                    html: {{ Js::from($value ?? '') }},
                    exec(cmd, val = null) { document.execCommand(cmd, false, val); this.$refs.editor.focus(); },
                    syncHidden() { this.$refs.hidden.value = this.$refs.editor.innerHTML; },
                }"
                class="form-control w-full"
            >
                @if($label)
                    <label class="label">
                        <span class="label-text font-medium">
                            {{ $label }}
                            @if($required) <span class="text-error ml-0.5">*</span> @endif
                        </span>
                    </label>
                @endif

                {{-- Toolbar --}}
                <div @class([
                    'flex flex-wrap gap-1 p-2 bg-base-200 border border-base-300 rounded-t-lg',
                    'border-error' => $error,
                ])>
                    @foreach([
                        ['cmd' => 'bold',          'icon' => '<b>B</b>',   'title' => 'Bold'],
                        ['cmd' => 'italic',        'icon' => '<i>I</i>',   'title' => 'Italic'],
                        ['cmd' => 'underline',     'icon' => '<u>U</u>',   'title' => 'Underline'],
                        ['cmd' => 'strikeThrough', 'icon' => '<s>S</s>',   'title' => 'Strikethrough'],
                    ] as $btn)
                        <button type="button" title="{{ $btn['title'] }}"
                            @click="exec('{{ $btn['cmd'] }}')"
                            class="btn btn-xs btn-ghost font-mono"
                        >{!! $btn['icon'] !!}</button>
                    @endforeach

                    <div class="divider divider-horizontal mx-0 my-1"></div>

                    <button type="button" title="Ordered list"  @click="exec('insertOrderedList')"   class="btn btn-xs btn-ghost">1.</button>
                    <button type="button" title="Bullet list"   @click="exec('insertUnorderedList')" class="btn btn-xs btn-ghost">•</button>

                    <div class="divider divider-horizontal mx-0 my-1"></div>

                    <select class="select select-xs" @change="exec('formatBlock', $event.target.value); $event.target.value = ''">
                        <option value="" disabled selected>Format</option>
                        <option value="p">Paragraph</option>
                        <option value="h2">Heading 2</option>
                        <option value="h3">Heading 3</option>
                        <option value="h4">Heading 4</option>
                        <option value="blockquote">Quote</option>
                    </select>

                    <div class="ml-auto flex gap-1">
                        <button type="button" @click="exec('undo')" class="btn btn-xs btn-ghost" title="Undo">↩</button>
                        <button type="button" @click="exec('redo')" class="btn btn-xs btn-ghost" title="Redo">↪</button>
                    </div>
                </div>

                {{-- Editable area --}}
                <div
                    x-ref="editor"
                    contenteditable="true"
                    @input="syncHidden()"
                    @blur="syncHidden()"
                    x-html="html"
                    data-placeholder="{{ $placeholder }}"
                    @class([
                        'min-h-[' . ($rows * 1.5) . 'rem] p-3 border border-t-0 border-base-300 rounded-b-lg bg-base-100 outline-none prose prose-sm max-w-none focus:border-primary',
                        'border-error' => $error,
                    ])
                ></div>

                {{-- Hidden input for form submission --}}
                <input type="hidden" name="{{ $name }}" x-ref="hidden" :value="html" @if($required) required @endif />

                @if($error)
                    <label class="label"><span class="label-text-alt text-error">{{ $error }}</span></label>
                @elseif($helper)
                    <label class="label"><span class="label-text-alt text-base-content/60">{{ $helper }}</span></label>
                @endif
            </div>

            <style>
                [contenteditable]:empty:before {
                    content: attr(data-placeholder);
                    color: oklch(var(--bc) / 0.4);
                    pointer-events: none;
                }
            </style>
            BLADE;
    }
}
