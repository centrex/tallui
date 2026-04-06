<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Concerns;

use Centrex\TallUi\DataTable\Filter;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

trait WithFilters
{
    /**
     * Active filter values keyed by Filter::stateKey().
     * URL-synced so filters survive page navigation.
     *
     * @var array<string, mixed>
     */
    #[Url(as: 'f', history: true)]
    public array $tableFilters = [];

    /** Whether the filter panel is expanded in the UI. */
    public bool $filtersOpen = false;

    /**
     * Override in the host component to define filters.
     *
     * @return array<int, Filter>
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Serialized filter definitions for the view (no closures).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFilterDefs(): array
    {
        return array_map(fn (Filter $f): array => $f->toArray(), $this->filters());
    }

    /** Count active (non-empty) filter values. */
    public function activeFilterCount(): int
    {
        return count(array_filter(
            $this->tableFilters,
            fn (mixed $v): bool => $v !== '' && $v !== null && $v !== [],
        ));
    }

    /** Reset all filter values and close the panel. */
    public function resetFilters(): void
    {
        $this->tableFilters = [];
        $this->filtersOpen = false;
        $this->resetPage();
    }

    /** Reset a single filter key. */
    public function resetFilter(string $key): void
    {
        unset($this->tableFilters[$key]);
        $this->resetPage();
    }

    public function toggleFilters(): void
    {
        $this->filtersOpen = !$this->filtersOpen;
    }

    public function updatedTableFilters(): void
    {
        $this->resetPage();
    }

    /**
     * Apply all active filters to the query.
     * Called from DataTable::buildQuery() via applyFilters().
     */
    public function applyFilters(Builder $query): Builder
    {
        foreach ($this->filters() as $filter) {
            $this->applyFilter($query, $filter);
        }

        return $query;
    }

    private function applyFilter(Builder $query, Filter $filter): void
    {
        match ($filter->type) {
            Filter::TYPE_TEXT       => $this->applyTextFilter($query, $filter),
            Filter::TYPE_SELECT     => $this->applySelectFilter($query, $filter),
            Filter::TYPE_DATE       => $this->applyDateFilter($query, $filter),
            Filter::TYPE_DATE_RANGE => $this->applyDateRangeFilter($query, $filter),
            Filter::TYPE_BOOLEAN    => $this->applyBooleanFilter($query, $filter),
            default                 => null,
        };
    }

    private function applyTextFilter(Builder $query, Filter $filter): void
    {
        $value = $this->tableFilters[$filter->column] ?? '';

        if ($value !== '' && $value !== null) {
            $query->where($filter->column, 'like', '%' . $value . '%');
        }
    }

    private function applySelectFilter(Builder $query, Filter $filter): void
    {
        $value = $this->tableFilters[$filter->column] ?? '';

        if ($value === '' || $value === null) {
            return;
        }

        if ($filter->multiple && is_array($value) && count($value) > 0) {
            $query->whereIn($filter->column, $value);
        } elseif (!$filter->multiple && $value !== '') {
            $query->where($filter->column, $value);
        }
    }

    private function applyDateFilter(Builder $query, Filter $filter): void
    {
        $value = $this->tableFilters[$filter->column] ?? '';

        if ($value !== '' && $value !== null) {
            $query->whereDate($filter->column, $value);
        }
    }

    private function applyDateRangeFilter(Builder $query, Filter $filter): void
    {
        $fromKey = $filter->column . '_from';
        $toKey = ($filter->toColumn ?? $filter->column) . '_to';

        $from = $this->tableFilters[$fromKey] ?? '';
        $to = $this->tableFilters[$toKey] ?? '';

        if ($from !== '' && $from !== null) {
            $query->whereDate($filter->column, '>=', $from);
        }

        if ($to !== '' && $to !== null) {
            $query->whereDate($filter->toColumn ?? $filter->column, '<=', $to);
        }
    }

    private function applyBooleanFilter(Builder $query, Filter $filter): void
    {
        $value = $this->tableFilters[$filter->column] ?? '';

        if ($value === '' || $value === null) {
            return;
        }

        $query->where($filter->column, (bool) $value);
    }
}
