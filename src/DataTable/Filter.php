<?php

declare(strict_types = 1);

namespace Centrex\TallUi\DataTable;

class Filter
{
    public const TYPE_TEXT       = 'text';
    public const TYPE_SELECT     = 'select';
    public const TYPE_DATE       = 'date';
    public const TYPE_DATE_RANGE = 'date_range';
    public const TYPE_BOOLEAN    = 'boolean';

    /** @var array<int|string, string> */
    public array $options = [];

    public ?string $placeholder = null;
    public bool $multiple       = false;

    /**
     * For date_range: the "to" column key.
     * The primary $column is used as the "from" column.
     */
    public ?string $toColumn = null;

    public function __construct(
        public readonly string $label,
        public readonly string $column,
        public readonly string $type = self::TYPE_TEXT,
    ) {}

    // ── Factories ──────────────────────────────────────────────────────────

    public static function text(string $label, string $column): static
    {
        return new static($label, $column, self::TYPE_TEXT);
    }

    /**
     * @param  array<int|string, string>  $options
     */
    public static function select(string $label, string $column, array $options = []): static
    {
        $filter          = new static($label, $column, self::TYPE_SELECT);
        $filter->options = $options;

        return $filter;
    }

    public static function date(string $label, string $column): static
    {
        return new static($label, $column, self::TYPE_DATE);
    }

    /**
     * Date range filter — $column is the "from" DB column, $toColumn is the "to" column.
     * They can be the same column (e.g. 'created_at' between two dates).
     */
    public static function dateRange(string $label, string $fromColumn, ?string $toColumn = null): static
    {
        $filter           = new static($label, $fromColumn, self::TYPE_DATE_RANGE);
        $filter->toColumn = $toColumn ?? $fromColumn;

        return $filter;
    }

    public static function boolean(string $label, string $column): static
    {
        return new static($label, $column, self::TYPE_BOOLEAN);
    }

    // ── Fluent modifiers ───────────────────────────────────────────────────

    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Allow multiple selections (select type only).
     */
    public function multiple(): static
    {
        $this->multiple = true;

        return $this;
    }

    // ── State keys ─────────────────────────────────────────────────────────

    /**
     * The state key(s) this filter writes into $tableFilters.
     * Date-range filters use two keys: "{column}_from" and "{column}_to".
     *
     * @return array<string>
     */
    public function stateKeys(): array
    {
        if ($this->type === self::TYPE_DATE_RANGE) {
            return [$this->column . '_from', ($this->toColumn ?? $this->column) . '_to'];
        }

        return [$this->column];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label'       => $this->label,
            'column'      => $this->column,
            'type'        => $this->type,
            'options'     => $this->options,
            'placeholder' => $this->placeholder,
            'multiple'    => $this->multiple,
            'toColumn'    => $this->toColumn,
            'stateKeys'   => $this->stateKeys(),
        ];
    }
}
