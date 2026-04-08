<div
    x-data="{
        digits: Array({{ $length }}).fill(''),
        get value() { return this.digits.join(''); },
        focus(i) {
            const el = this.$refs['pin_' + i];
            if (el) el.focus();
        },
        onInput(i, e) {
            const val = e.target.value.replace(/\D/g, '');
            if (!val) { this.digits[i] = ''; return; }
            // handle paste: distribute across inputs
            if (val.length > 1) {
                const chars = val.split('').slice(0, {{ $length }} - i);
                chars.forEach((c, j) => { if (i + j < {{ $length }}) this.digits[i + j] = c; });
                const next = Math.min(i + chars.length, {{ $length }} - 1);
                this.$nextTick(() => this.focus(next));
                return;
            }
            this.digits[i] = val;
            e.target.value = val;
            if (i < {{ $length - 1 }}) this.$nextTick(() => this.focus(i + 1));
        },
        onKeydown(i, e) {
            if (e.key === 'Backspace') {
                if (this.digits[i]) { this.digits[i] = ''; }
                else if (i > 0) { this.digits[i-1] = ''; this.focus(i-1); }
            } else if (e.key === 'ArrowLeft' && i > 0) {
                this.focus(i - 1);
            } else if (e.key === 'ArrowRight' && i < {{ $length - 1 }}) {
                this.focus(i + 1);
            }
        },
        onPaste(e) {
            e.preventDefault();
            const text = (e.clipboardData || window.clipboardData).getData('text');
            const clean = {{ $numeric ? "text.replace(/\\D/g, '')" : 'text' }}.slice(0, {{ $length }});
            clean.split('').forEach((c, j) => { this.digits[j] = c; });
            this.$nextTick(() => this.focus(Math.min(clean.length, {{ $length - 1 }})));
        },
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

    <div class="flex items-center gap-2" @paste="onPaste">
        @for($i = 0; $i < $length; $i++)
            <input
                x-ref="pin_{{ $i }}"
                type="{{ $masked ? 'password' : 'text' }}"
                inputmode="{{ $numeric ? 'numeric' : 'text' }}"
                maxlength="{{ $masked ? 1 : $length }}"
                :value="digits[{{ $i }}]"
                @input="onInput({{ $i }}, $event)"
                @keydown="onKeydown({{ $i }}, $event)"
                @focus="$event.target.select()"
                @class([
                    'input input-bordered text-center font-mono font-bold tracking-widest',
                    $inputSizeClass,
                    'input-error' => $error,
                ])
                autocomplete="one-time-code"
            />
            @if($i === intdiv($length, 2) - 1 && $length > 4)
                <span class="text-base-content/30 font-bold select-none">–</span>
            @endif
        @endfor
    </div>

    {{-- Hidden consolidated input --}}
    <input
        type="hidden"
        name="{{ $name }}"
        :value="value"
        @if($required) x-bind:required="value.length < {{ $length }}" @endif
    />

    @if($error)
        <label class="label"><span class="label-text-alt text-error">{{ $error }}</span></label>
    @elseif($helper)
        <label class="label"><span class="label-text-alt text-base-content/60">{{ $helper }}</span></label>
    @endif
</div>
