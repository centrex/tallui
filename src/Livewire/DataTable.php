<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire;

use Centrex\TallUi\Concerns\CachesData;
use Centrex\TallUi\DataTable\Column;
use Illuminate\Contracts\Pagination\{LengthAwarePaginator as LengthAwarePaginatorContract, Paginator as PaginatorContract};
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
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

    public string $defaultSortBy = '';

    public string $defaultSortDirection = 'asc';

    // ── Pagination ────────────────────────────────────────────────────────

    public int $perPage = 15;

    /**
     * Use "simple" pagination (Prev/Next only, no total count or page jump)
     * instead of the default length-aware pagination. LengthAwarePaginator
     * runs a COUNT(*) query on every request — on very large tables (hundreds
     * of thousands+ rows) that count is often the single most expensive part
     * of the request. Enable this on huge tables where an exact total isn't
     * essential to the UX.
     */
    public bool $simplePagination = false;

    // ── Row selection ─────────────────────────────────────────────────────

    /** Primary key column name used for row selection and export. */
    public string $primaryKey = 'id';

    /** @var array<int, string> Selected row IDs (stored as strings). */
    public array $selectedRows = [];

    /**
     * True once the user has explicitly opted into selecting every row that
     * matches the current search/filters — not just the ones on this page.
     * Bulk actions (export, or custom actions built on $selectedRows) should
     * treat this the same as an empty $selectedRows: every matching row.
     */
    public bool $selectAllMatching = false;

    // ── Appearance / theme ───────────────────────────────────────────────

    /** Zebra-striped rows. Defaults from config('tallui.datatable.striped'). */
    public bool $striped = true;

    /**
     * Header visual treatment: 'default' (subtle bg-base-50) | 'minimal'
     * (no background, border only) | 'bold' (bg-base-200, heavier text) |
     * 'primary' (tinted with the brand color). Defaults from
     * config('tallui.datatable.header_style').
     */
    public string $headerStyle = 'default';

    /** Show the rows-per-page selector in the toolbar. */
    public bool $showPerPageSelector = true;

    /** Show the "manage columns" visibility toggle in the toolbar. */
    public bool $showColumnToggle = true;

    /**
     * Bound the table body to a fixed height with internal scroll and a sticky
     * header, e.g. '70vh' or '600px'. Empty string = unbounded (default).
     */
    public string $maxHeight = '';

    // ── Responsive ────────────────────────────────────────────────────────

    /**
     * Tailwind breakpoint at which the table view replaces the mobile card stack.
     * Below this breakpoint rows are rendered as stacked cards.
     * Set to '' to disable the card stack (table-only).
     */
    public string $mobileBreakpoint = 'lg';

    /**
     * Column keys the user has hidden via the column-visibility toggle.
     * URL-synced so the choice survives navigation/reload.
     *
     * @var array<int, string>
     */
    #[Url(as: 'cols', history: true)]
    public array $hiddenColumns = [];

    /**
     * Remember the authenticated user's column-visibility choices across
     * sessions/devices (keyed by user id + table class), not just for the
     * current URL. Host components can set this to false to opt out;
     * config('tallui.datatable.persist_column_preferences') is a global
     * kill switch. A shared link's `cols` query param always wins over the
     * stored preference for that page load.
     */
    public bool $persistColumnPreferences = true;

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
     * Count active (non-empty) filter values.
     *
     * Default no-op fallback for host components that don't use WithFilters —
     * the trait provides the real implementation and takes precedence when present.
     */
    public function activeFilterCount(): int
    {
        return 0;
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
        $this->striped = (bool) config('tallui.datatable.striped', true);
        $this->headerStyle = (string) config('tallui.datatable.header_style', 'default');
        $this->columnDefs = array_map(
            fn (Column $col): array => $col->toArray(),
            $this->columns(),
        );

        $this->perPage = $this->normalizePerPage($this->perPage);

        if (!in_array($this->headerStyle, ['default', 'minimal', 'bold', 'primary'], true)) {
            $this->headerStyle = 'default';
        }

        $this->persistColumnPreferences = $this->persistColumnPreferences
            && (bool) config('tallui.datatable.persist_column_preferences', true);

        // No `cols` query param on this request (fresh visit, not a shared
        // link) — seed from the user's remembered preference, if any.
        if ($this->persistColumnPreferences && !request()->has('cols') && auth()->check()) {
            $this->hiddenColumns = $this->loadColumnPreferences();
        }

        // Drop any hidden-column keys (e.g. stale URL/stored state) that no
        // longer correspond to a real, non-actions column.
        $validKeys = array_column($this->columnDefs, 'key');
        $this->hiddenColumns = array_values(array_intersect($this->hiddenColumns, $validKeys));

        if ($this->sortBy === '' && $this->defaultSortBy !== '' && $this->isSortableColumn($this->defaultSortBy)) {
            $this->sortBy = $this->defaultSortBy;
            $this->sortDirection = strtolower($this->defaultSortDirection) === 'desc' ? 'desc' : 'asc';
        } elseif (!$this->isValidSortDirection($this->sortDirection)) {
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Skeleton shown while the component is lazy-loaded, e.g.
     * <livewire:tallui-data-table lazy /> — Livewire renders this
     * placeholder on first paint, then swaps in the real table once the
     * (potentially expensive) initial query resolves.
     */
    public function placeholder(): View
    {
        return view('tallui::livewire.data-table-placeholder', [
            'columnCount' => max(count($this->columns()), 1),
            'rowCount'    => min($this->perPage ?: 10, 10),
        ]);
    }

    // ── Actions ───────────────────────────────────────────────────────────

    public function sort(string $column): void
    {
        if (!$this->isSortableColumn($column)) {
            return;
        }

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

        if ($this->selectAllMatching) {
            // Escape hatch: rather than tracking an "everything except these"
            // exclusion set, deselecting while "all matching" is active just
            // starts a fresh, explicit single-row selection.
            $this->selectAllMatching = false;
            $this->selectedRows = [$id];

            return;
        }

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
        $this->selectAllMatching = false;

        $rows = $this->getRows();
        $pageIds = collect($rows->items())->map(fn ($r): string => (string) data_get($r, $this->primaryKey))->all();
        $allSelected = array_diff($pageIds, $this->selectedRows) === [];

        if ($allSelected) {
            $this->selectedRows = array_values(array_diff($this->selectedRows, $pageIds));
        } else {
            $this->selectedRows = array_values(array_unique(array_merge($this->selectedRows, $pageIds)));
        }
    }

    /**
     * Opt into selecting every row matching the current search/filters, not
     * just the ones visible on this page. Bulk actions built on $selectedRows
     * should check $selectAllMatching first and, when true, operate on the
     * full filtered query (see buildExportQuery()) instead of iterating ids.
     */
    public function selectAllAcrossPages(): void
    {
        $this->selectAllMatching = true;
        $this->selectedRows = [];
    }

    public function clearSelection(): void
    {
        $this->selectedRows = [];
        $this->selectAllMatching = false;
    }

    // ── Column visibility (responsive / user-controlled columns) ───────────

    /**
     * Show or hide a data column. Display-only — hidden columns are still
     * searchable/sortable and are always included in CSV export.
     */
    public function toggleColumnVisibility(string $key): void
    {
        if (in_array($key, $this->hiddenColumns, true)) {
            $this->hiddenColumns = array_values(array_diff($this->hiddenColumns, [$key]));
        } else {
            $this->hiddenColumns[] = $key;
        }

        $this->saveColumnPreferences();
    }

    public function resetColumnVisibility(): void
    {
        $this->hiddenColumns = [];

        $this->saveColumnPreferences();
    }

    /**
     * Cache key scoped to the current user and this table's class, so
     * distinct DataTable subclasses (e.g. InvoiceTable vs BillTable) each
     * remember their own column choices.
     */
    protected function columnPreferenceCacheKey(): string
    {
        return $this->cacheKey('column-prefs', (string) auth()->id(), static::class);
    }

    /**
     * @return array<int, string>
     */
    protected function loadColumnPreferences(): array
    {
        $stored = Cache::store($this->cacheStore)->get($this->columnPreferenceCacheKey(), []);

        return is_array($stored) ? $stored : [];
    }

    protected function saveColumnPreferences(): void
    {
        if (!$this->persistColumnPreferences || !auth()->check()) {
            return;
        }

        Cache::store($this->cacheStore)->forever(
            $this->columnPreferenceCacheKey(),
            $this->hiddenColumns,
        );
    }

    /**
     * Column definitions with hidden ones removed — used for rendering only.
     * Action columns (no key) are never hideable and always pass through.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function visibleColumnDefs(): array
    {
        return array_values(array_filter(
            $this->columnDefs,
            fn (array $col): bool => $col['key'] === null || !in_array($col['key'], $this->hiddenColumns, true),
        ));
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
        $this->eagerLoadRelationColumns($query);

        if ($this->searchIsActive()) {
            $searchableCols = array_filter(
                $this->columnDefs,
                fn (array $col): bool => ($col['searchable'] ?? false) && $col['key'] !== null,
            );

            if ($searchableCols !== []) {
                $query->where(function (Builder $q) use ($searchableCols): void {
                    foreach ($searchableCols as $col) {
                        if ($col['key'] !== null) {
                            $this->applySearchConstraint($q, $col['key'], $this->search);
                        }
                    }
                });
            }
        }

        $this->applyFilters($query);

        if ($this->selectedRows !== []) {
            $query->whereIn($this->primaryKey, $this->selectedRows);
        }

        if ($this->sortBy !== '' && $this->isSortableColumn($this->sortBy)) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } elseif ($this->defaultSortBy !== '' && $this->isSortableColumn($this->defaultSortBy)) {
            $query->orderBy($this->defaultSortBy, $this->defaultSortDirection === 'desc' ? 'desc' : 'asc');
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
                            : Column::scalarValue($value);
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
        $this->perPage = $this->normalizePerPage($this->perPage);
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
     */
    protected function buildQuery(): PaginatorContract
    {
        $query = $this->query();
        $this->eagerLoadRelationColumns($query);

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
                            $this->applySearchConstraint($q, $col['key'], $this->search);
                        }
                    }
                });
            }
        }

        // Typed filters (via WithFilters trait or simple fallback)
        $this->applyFilters($query);

        // Sorting
        if ($this->sortBy !== '' && $this->isSortableColumn($this->sortBy)) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        } elseif ($this->defaultSortBy !== '' && $this->isSortableColumn($this->defaultSortBy)) {
            $query->orderBy($this->defaultSortBy, $this->defaultSortDirection === 'desc' ? 'desc' : 'asc');
        }

        return $this->simplePagination
            ? $query->simplePaginate($this->perPage)
            : $query->paginate($this->perPage);
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
                'simple'  => $this->simplePagination,
                'filters' => $filterState,
            ])),
        );
    }

    public function getRows(): PaginatorContract
    {
        return $this->rememberCacheTracked(
            $this->dataTableCacheKey(),
            fn (): PaginatorContract => $this->buildQuery(),
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

    /**
     * Eager-load every relation referenced by a ->relation() column so that
     * rendering "type.name"-style dot-notation cells doesn't lazy-load the
     * relation once per row (an N+1 query per page).
     */
    protected function eagerLoadRelationColumns(Builder $query): void
    {
        /** @var array<int, string> $relations */
        $relations = array_values(array_unique(array_filter(
            array_column($this->columnDefs, 'relation'),
            fn (mixed $relation): bool => is_string($relation) && $relation !== '',
        )));

        if ($relations !== []) {
            $query->with($relations);
        }
    }

    /**
     * Column key => sum, for every ->summable() column, computed across every
     * row matching the current search/filters/selection — not just the
     * current page. Cached alongside the main query.
     *
     * @return array<string, float>
     */
    public function getColumnSums(): array
    {
        $summableKeys = array_values(array_filter(array_map(
            fn (array $col): ?string => ($col['summable'] ?? false) && $col['key'] !== null && !str_contains((string) $col['key'], '.')
                ? $col['key']
                : null,
            $this->columnDefs,
        )));

        if ($summableKeys === []) {
            return [];
        }

        return $this->rememberCacheTracked(
            $this->dataTableSumsCacheKey(),
            function () use ($summableKeys): array {
                // reorder(): ORDER BY is irrelevant to an aggregate and can
                // prevent the query planner from using a covering index.
                $query = $this->buildExportQuery()->reorder();
                $sums = [];

                foreach ($summableKeys as $key) {
                    $sums[$key] = (float) (clone $query)->sum($key);
                }

                return $sums;
            },
        );
    }

    protected function dataTableSumsCacheKey(): string
    {
        $filterState = property_exists($this, 'tableFilters') ? $this->tableFilters : [];

        return $this->cacheKey(
            'datatable-sums',
            md5(static::class . serialize([
                'search'            => $this->search,
                'filters'           => $filterState,
                'selected'          => $this->selectedRows,
                'selectAllMatching' => $this->selectAllMatching,
            ])),
        );
    }

    protected function applySearchConstraint(Builder $query, string $column, string $search): void
    {
        if (!str_contains($column, '.')) {
            $query->orWhere($column, 'like', '%' . $search . '%');

            return;
        }

        $segments = explode('.', $column);
        $field = array_pop($segments);
        $relation = implode('.', $segments);

        $query->orWhereHas($relation, function (Builder $relationQuery) use ($field, $search): void {
            $relationQuery->where($field, 'like', '%' . $search . '%');
        });
    }

    protected function sortableColumns(): array
    {
        return array_values(array_map(
            fn (array $col): string => (string) $col['key'],
            array_filter(
                $this->columnDefs,
                fn (array $col): bool => ($col['sortable'] ?? false) && !empty($col['key']) && !str_contains((string) $col['key'], '.'),
            ),
        ));
    }

    protected function isSortableColumn(string $column): bool
    {
        return in_array($column, $this->sortableColumns(), true);
    }

    protected function isValidSortDirection(string $direction): bool
    {
        return in_array($direction, ['asc', 'desc'], true);
    }

    protected function normalizePerPage(int $perPage): int
    {
        $allowed = $this->perPageOptions();

        return in_array($perPage, $allowed, true)
            ? $perPage
            : (int) ($allowed[0] ?? 15);
    }

    public function render(): View
    {
        $filterDefs = method_exists($this, 'getFilterDefs') ? $this->getFilterDefs() : [];
        $rows = $this->getRows();

        $pageIds = collect($rows->items())->map(fn ($r): string => (string) data_get($r, $this->primaryKey))->all();
        $selectedOnPage = count(array_intersect($pageIds, $this->selectedRows));
        $totalOnPage = count($pageIds);
        $isLengthAware = $rows instanceof LengthAwarePaginatorContract;

        return view('tallui::livewire.data-table', [
            'rows'                  => $rows,
            'columns'               => $this->visibleColumnDefs(),
            'allColumns'            => $this->columnDefs,
            'filterDefs'            => $filterDefs,
            'primaryKey'            => $this->primaryKey,
            'striped'               => $this->striped,
            'columnSums'            => $this->getColumnSums(),
            'pageFullySelected'     => $totalOnPage > 0 && ($selectedOnPage === $totalOnPage || $this->selectAllMatching),
            'pagePartiallySelected' => $selectedOnPage > 0 && $selectedOnPage < $totalOnPage && !$this->selectAllMatching,
            'hasMoreThanPage'       => $isLengthAware ? $rows->total() > $totalOnPage : $rows->hasMorePages(),
            'totalMatching'         => $isLengthAware ? $rows->total() : null,
        ]);
    }
}
