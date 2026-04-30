<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components\Form;

use Centrex\TallUi\Concerns\HasUuid;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FileUpload extends Component
{
    use HasUuid;

    public function __construct(
        public string $name = '',
        public ?string $label = null,
        public ?string $helper = null,
        public ?string $error = null,
        public bool $multiple = false,
        public ?string $accept = null,           // e.g. "image/*,.pdf"
        public int $maxSizeMb = 10,
        public bool $preview = true,             // show image previews
        public bool $required = false,
        public ?string $id = null,
        public string $uploadText = 'Drop files here or click to upload',
        public string $uploadSubtext = '',
    ) {
        $this->generateUuid($id);
        $this->uploadSubtext = $uploadSubtext ?: "Max {$maxSizeMb}MB" . ($accept ? " · {$accept}" : '');
    }

    public function render(): View|Closure|string
    {
        return <<<'BLADE'
            @php
                $inputAttributes = $attributes
                    ->whereDoesntStartWith('class')
                    ->merge([]);
            @endphp

            <div
                x-data="{
                    files: [],
                    dragging: false,
                    maxBytes: {{ $maxSizeMb * 1024 * 1024 }},
                    accept: {{ Js::from($accept) }},
                    errors: [],

                    handleFiles(newFiles) {
                        this.errors = [];
                        this.files = {{ $multiple ? 'this.files' : '[]' }};

                        Array.from(newFiles).forEach(file => {
                            if (this.maxBytes && file.size > this.maxBytes) {
                                this.errors.push(`${file.name} exceeds {{ $maxSizeMb }}MB limit`);
                                return;
                            }
                            const reader = new FileReader();
                            reader.onload = e => {
                                this.files.push({
                                    name: file.name,
                                    size: file.size,
                                    type: file.type,
                                    preview: file.type.startsWith('image/') ? e.target.result : null,
                                    raw: file,
                                });
                            };
                            reader.readAsDataURL(file);
                        });
                    },
                    remove(i) {
                        this.files.splice(i, 1);

                        if (this.files.length === 0 && this.$refs.fileInput) {
                            this.$refs.fileInput.value = '';
                            this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    },
                    formatSize(bytes) {
                        if (bytes < 1024) return bytes + ' B';
                        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
                        return (bytes / 1048576).toFixed(1) + ' MB';
                    },
                    triggerInput() { this.$refs.fileInput.click(); },
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

                {{-- Drop zone --}}
                <div
                    @click="triggerInput()"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; handleFiles($event.dataTransfer.files)"
                    :class="dragging ? 'border-primary bg-primary/5' : 'border-base-300 hover:border-primary/60 hover:bg-base-200/50'"
                    @class([
                        'border-2 border-dashed rounded-xl p-6 flex flex-col items-center justify-center gap-2 cursor-pointer transition-colors duration-150',
                        'border-error' => $error,
                    ])
                >
                    <x-tallui-icon name="o-cloud-arrow-up" class="w-10 h-10 text-base-content/30" />
                    <p class="text-sm font-medium text-base-content/70">{{ $uploadText }}</p>
                    <p class="text-xs text-base-content/40">{{ $uploadSubtext }}</p>

                    <input
                        type="file"
                        id="{{ $id }}"
                        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
                        x-ref="fileInput"
                        @change="handleFiles($event.target.files)"
                        @if($multiple) multiple @endif
                        @if($accept) accept="{{ $accept }}" @endif
                        @if($required) required @endif
                        {{ $inputAttributes }}
                        class="sr-only"
                    />
                </div>

                {{-- Error messages (size/type violations) --}}
                <template x-for="err in errors" :key="err">
                    <p x-text="err" class="text-xs text-error mt-1"></p>
                </template>

                {{-- File previews --}}
                @if($preview)
                    <div x-show="files.length > 0" class="mt-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        <template x-for="(file, i) in files" :key="i">
                            <div class="relative group rounded-lg border border-base-300 overflow-hidden bg-base-200">
                                {{-- Image preview --}}
                                <template x-if="file.preview">
                                    <img :src="file.preview" :alt="file.name" class="w-full h-24 object-cover" />
                                </template>
                                {{-- File icon for non-images --}}
                                <template x-if="!file.preview">
                                    <div class="w-full h-24 flex items-center justify-center text-base-content/40">
                                        <x-tallui-icon name="o-document" class="w-10 h-10" />
                                    </div>
                                </template>
                                {{-- Overlay with info --}}
                                <div class="absolute inset-x-0 bottom-0 bg-black/60 px-2 py-1 text-white">
                                    <p x-text="file.name" class="text-xs truncate"></p>
                                    <p x-text="formatSize(file.size)" class="text-xs opacity-70"></p>
                                </div>
                                {{-- Remove button --}}
                                <button
                                    type="button"
                                    @click.stop="remove(i)"
                                    class="absolute top-1 right-1 btn btn-xs btn-circle btn-error opacity-0 group-hover:opacity-100 transition-opacity"
                                    aria-label="Remove file"
                                >✕</button>
                            </div>
                        </template>
                    </div>
                @endif

                @if($error)
                    <label class="label"><span class="label-text-alt text-error">{{ $error }}</span></label>
                @elseif($helper)
                    <label class="label"><span class="label-text-alt text-base-content/60">{{ $helper }}</span></label>
                @endif
            </div>
            BLADE;
    }
}
