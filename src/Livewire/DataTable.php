<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Livewire;

use Centrex\TallUi\Concerns\CachesData;
use Centrex\TallUi\DataTable\Column;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

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
        $this->perPage    = (int) config('tallui.datatable.per_page', 15);
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
            $this->sortBy        = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
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

            if (count($searchableCols) > 0) {
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

        return view('tallui::livewire.data-table', [
            'rows'       => $this->getRows(),
            'columns'    => $this->columnDefs,
            'filterDefs' => $filterDefs,
        ]);
    }
}
