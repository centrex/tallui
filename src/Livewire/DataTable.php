<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire;

use Centrex\TallUi\Concerns\CachesData;
use Centrex\TallUi\DataTable\Column;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\{Component, WithPagination};
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataTable extends Component
{
    use CachesData;
    use WithPagination;

    // ── Search ────────────────────────────────────────────────────────────

    #[Url(as: 'search', history: true)]
    public string $search = '';

    /**
     * Minimum character count before search is applied.
     * 0 = search on every keystroke.
     */
    public int $minSearchLength = 0;

    // ── Sorting ───────────────────────────────────────────────────────────

    #[Url(as: 'sort', history: true)]
    public string $sortBy = '';

    #[Url(as: 'dir', history: true)]
    public string $sortDirection = 'asc';

    // ── Pagination ────────────────────────────────────────────────────────

    public int $perPage = 15;

    // ── Row selection ─────────────────────────────────────────────────────

    /** Primary key column name used for row selection and export. */
    public string $primaryKey = 'id';

    /** @var array<int, string> Selected row IDs (stored as strings). */
    public array $selectedRows = [];

    // ── Responsive ────────────────────────────────────────────────────────

    /**
     * Tailwind breakpoint at which the table view replaces the mobile card stack.
     * Below this breakpoint rows are rendered as stacked cards.
     * Set to '' to disable the card stack (table-only).
     */
    public string $mobileBreakpoint = 'lg';

    // ── Column definitions (serialization-safe) ───────────────────────────

    /** @var array<int, array<string, mixed>> */
    public array $columnDefs = [];

    // ── Overridable by host component ─────────────────────────────────────

    /**
     * Define columns. Override in your host component.
     *
     * @return array<int, Column>
     */
    public function columns(): array
    {
        return [];
    }

    /**
     * Provide the base Eloquent query. Override in your host component.
     */
    public function query(): Builder
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model>|null $model */
        $model = property_exists($this, 'model') ? $this->model : null;

        if ($model !== null && class_exists($model)) {
            return $model::query();
        }

        throw new \RuntimeException(
            'DataTable requires either a $model property or an overridden query() method.',
        );
    }

    /**
     * Apply filters to the query.
     *
     * Default implementation handles the flat $tableFilters array (if WithFilters
     * is not used) or defers to WithFilters::applyFilters() when the trait is present.
     */
    public function applyFilters(Builder $query): Builder
    {
        // If the host component uses WithFilters trait, it overrides this method.
        // Fallback: apply any manually-set $tableFilters as simple equality checks.
        if (property_exists($this, 'tableFilters')) {
            /** @var array<string, mixed> $tableFilters */
            $tableFilters = $this->tableFilters;

            foreach ($tableFilters as $key => $value) {
                if ($value !== '' && $value !== null && $value !== []) {
                    $query->where($key, $value);
                }
            }
        }

        return $query;
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->perPage = (int) config('tallui.datatable.per_page', 15);
        $this->columnDefs = array_map(
            fn (Column $col): array => $col->toArray(),
            $this->columns(),
        );
    }

    // ── Actions ───────────────────────────────────────────────────────────

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    // ── Row selection ─────────────────────────────────────────────────────

    public function toggleRow(mixed $rowId): void
    {
        $id = (string) $rowId;

        if (in_array($id, $this->selectedRows, true)) {
            $this->selectedRows = array_values(
                array_filter($this->selectedRows, fn (string $v): bool => $v !== $id),
            );
        } else {
            $this->selectedRows[] = $id;
        }
    }

    public function togglePageSelection(): void
    {
        $rows = $this->getRows();
        $pageIds = $rows->map(fn ($r): string => (string) data_get($r, $this->primaryKey))->all();
        $allSelected = array_diff($pageIds, $this->selectedRows) === [];

        if ($allSelected) {
            $this->selectedRows = array_values(array_diff($this->selectedRows, $pageIds));
        } else {
            $this->selectedRows = array_values(array_unique(array_merge($this->selectedRows, $pageIds)));
        }
    }

    public function clearSelection(): void
    {
        $this->selectedRows = [];
    }

    // ── Export ────────────────────────────────────────────────────────────

    /**
     * Returns exportable column definitions (no action columns, must have a key).
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getExportableColumns(): array
    {
        return array_values(array_filter(
            $this->columnDefs,
            fn (array $col): bool => ($col['exportable'] ?? true)
                && !$col['isActions']
                && $col['key'] !== null,
        ));
    }

    /**
     * Build a non-paginated query for export, respecting current search/filters.
     * When rows are selected only those rows are exported; otherwise all matching rows.
     */
    protected function buildExportQuery(): Builder
    {
        $query = $this->query();

        if ($this->searchIsActive()) {
            $searchableCols = array_filter(
                $this->columnDefs,
                fn (array $col): bool => ($col['searchable'] ?? false) && $col['key'] !== null,
            );

            if ($searchableCols !== []) {
                $query->where(function (Builder $q) use ($searchableCols): void {
                    foreach ($searchableCols as $col) {
                        if ($col['key'] !== null) {
                            $q->orWhere($col['key'], 'like', '%' . $this->search . '%');
                        }
                    }
                });
            }
        }

        $this->applyFilters($query);

        if ($this->selectedRows !== []) {
            $query->whereIn($this->primaryKey, $this->selectedRows);
        }

        if ($this->sortBy !== '') {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query;
    }

    /**
     * Stream a UTF-8 CSV download (Excel-compatible via BOM).
     * Exports selected rows when a selection exists, otherwise all matching rows.
     */
    public function exportCsv(): StreamedResponse
    {
        $columns = $this->getExportableColumns();
        $query = $this->buildExportQuery();
        $label = $this->selectedRows === []
            ? 'all'
            : count($this->selectedRows) . '-rows';
        $filename = 'export-' . $label . '-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($columns, $query): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens it correctly
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, array_column($columns, 'label'));

            // Data rows – chunked to avoid memory spikes on large datasets
            $query->chunk(500, function ($rows) use ($handle, $columns): void {
                foreach ($rows as $row) {
                    $csvRow = [];

                    foreach ($columns as $col) {
                        $value = data_get($row, $col['key'] ?? '');
                        $csvRow[] = is_array($value)
                            ? implode(', ', $value)
                            : (string) ($value ?? '');
                    }
                    fputcsv($handle, $csvRow);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function updatedSearch(): void
    {
        // Only reset pagination when the search meets the minimum length,
        // or when clearing (empty string always applies).
        if ($this->search === '' || mb_strlen($this->search) >= $this->minSearchLength) {
            $this->resetPage();
        }
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    // ── Query building ────────────────────────────────────────────────────

    /**
     * Returns true if the current search term should be applied.
     */
    protected function searchIsActive(): bool
    {
        return $this->search !== ''
            && mb_strlen($this->search) >= $this->minSearchLength;
    }

    /**
     * Build and execute the data query. Extracted so caching wraps only this method.
     *
     * @return LengthAwarePaginator<\Illuminate\Database\Eloquent\Model>
     */
    protected function buildQuery(): LengthAwarePaginator
    {
        $query = $this->query();

        // Global full-text search across searchable columns
        if ($this->searchIsActive()) {
            $searchableCols = array_filter(
                $this->columnDefs,
                fn (array $col): bool => ($col['searchable'] ?? false) && $col['key'] !== null,
            );

            if ($searchableCols !== []) {
                $query->where(function (Builder $q) use ($searchableCols): void {
                    foreach ($searchableCols as $col) {
                        if ($col['key'] !== null) {
                            $q->orWhere($col['key'], 'like', '%' . $this->search . '%');
                        }
                    }
                });
            }
        }

        // Typed filters (via WithFilters trait or simple fallback)
        $this->applyFilters($query);

        // Sorting
        if ($this->sortBy !== '') {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    /**
     * Cache key encodes the full query state so every unique combination
     * is cached independently.
     */
    protected function dataTableCacheKey(): string
    {
        $filterState = property_exists($this, 'tableFilters') ? $this->tableFilters : [];

        return $this->cacheKey(
            'datatable',
            md5(static::class . serialize([
                'search'  => $this->search,
                'sort'    => $this->sortBy,
                'dir'     => $this->sortDirection,
                'page'    => $this->getPage(),
                'perPage' => $this->perPage,
                'filters' => $filterState,
            ])),
        );
    }

    /**
     * @return LengthAwarePaginator<\Illuminate\Database\Eloquent\Model>
     */
    public function getRows(): LengthAwarePaginator
    {
        return $this->rememberCacheTracked(
            $this->dataTableCacheKey(),
            fn (): LengthAwarePaginator => $this->buildQuery(),
        );
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /** @return array<int, int> */
    public function perPageOptions(): array
    {
        return config('tallui.datatable.per_page_options', [10, 15, 25, 50, 100]);
    }

    /**
     * Render a custom-HTML column cell safely.
     * Called from the Blade view for isHtml columns.
     *
     * @param  array<string, mixed>  $column
     * @param  \Illuminate\Database\Eloquent\Model|array<string, mixed>  $row
     */
    public function renderHtmlColumn(array $column, mixed $row): string
    {
        $value = data_get($row, $column['key'] ?? '');

        if ($column['htmlView'] !== null) {
            return view($column['htmlView'], ['row' => $row, 'value' => $value])->render();
        }

        if ($column['htmlRenderer'] !== null && class_exists($column['htmlRenderer'])) {
            /** @var \Centrex\TallUi\Contracts\ColumnRenderer $renderer */
            $renderer = app($column['htmlRenderer']);

            return $renderer->render($row, $value);
        }

        return e((string) ($value ?? ''));
    }

    public function render(): View
    {
        $filterDefs = method_exists($this, 'getFilterDefs') ? $this->getFilterDefs() : [];
        $rows = $this->getRows();

        $pageIds = $rows->map(fn ($r): string => (string) data_get($r, $this->primaryKey))->all();
        $selectedOnPage = count(array_intersect($pageIds, $this->selectedRows));
        $totalOnPage = count($pageIds);

        return view('tallui::livewire.data-table', [
            'rows'                  => $rows,
            'columns'               => $this->columnDefs,
            'filterDefs'            => $filterDefs,
            'primaryKey'            => $this->primaryKey,
            'pageFullySelected'     => $totalOnPage > 0 && $selectedOnPage === $totalOnPage,
            'pagePartiallySelected' => $selectedOnPage > 0 && $selectedOnPage < $totalOnPage,
        ]);
    }
}
