<div class="space-y-4" role="status" aria-label="Loading table">
    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-tallui-skeleton width="w-64" height="h-9" />
        <div class="flex items-center gap-2">
            <x-tallui-skeleton width="w-24" height="h-9" />
            <x-tallui-skeleton width="w-16" height="h-9" />
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl border border-base-200 overflow-hidden shadow-sm bg-base-100">
        <div class="border-b border-base-200 bg-base-50 px-4 py-3.5 flex gap-6">
            @for ($i = 0; $i < $columnCount; $i++)
                <x-tallui-skeleton width="w-20" height="h-3" />
            @endfor
        </div>
        <div class="divide-y divide-base-200">
            @for ($r = 0; $r < $rowCount; $r++)
                <div class="px-4 py-3.5 flex gap-6">
                    @for ($c = 0; $c < $columnCount; $c++)
                        <x-tallui-skeleton width="w-20" height="h-4" />
                    @endfor
                </div>
            @endfor
        </div>
    </div>
</div>
