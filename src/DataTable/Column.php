<?php

declare(strict_types = 1);

namespace Centrex\TallUi\DataTable;

class Column
{
    public bool $sortable = false;

    public bool $searchable = false;

    public bool $isBadge = false;

    public bool $isActions = false;

    public bool $isRaw = false;   // render value as {!! !!} trusted HTML

    public bool $isHtml = false;   // render via view or renderer class

    public bool $exportable = true;   // include in CSV export

    /**
     * Tailwind breakpoint at which this column becomes visible in the table.
     * e.g. 'sm', 'md', 'lg', 'xl', '2xl'. null = always visible.
     * Generates "hidden {bp}:table-cell" on <th> and <td>.
     */
    public ?string $visibleFrom = null;

    /** Named format hint for the view (e.g. 'currency', 'datetime'). */
    public ?string $format = null;

    /** DaisyUI badge color modifier when isBadge = true. */
    public ?string $badgeColor = null;

    /**
     * Map of value → DaisyUI badge modifier, e.g. ['active' => 'success', 'banned' => 'error'].
     *
     * @var array<string, string>
     */
    public array $badgeColors = [];

    /** Blade view name rendered for this cell. Receives $row and $value. */
    public ?string $htmlView = null;

    /**
     * FQCN of a class implementing Centrex\TallUi\Contracts\ColumnRenderer.
     * render($row, $value): string
     */
    public ?string $htmlRenderer = null;

    public ?string $relation = null;

    /** @var array<int, Action> */
    public array $actions = [];

    public function __construct(
        public readonly string $label,
        public readonly ?string $key = null,
    ) {}

    public static function make(string $label, ?string $key = null): static
    {
        return new static($label, $key);
    }

    // ── Display modifiers ─────────────────────────────────────────────────

    public function sortable(): static
    {
        $this->sortable = true;

        return $this;
    }

    public function searchable(): static
    {
        $this->searchable = true;

        return $this;
    }

    /**
     * Render the cell value as a DaisyUI badge.
     *
     * @param  array<string, string>  $colors  Map value → DaisyUI modifier (e.g. ['active' => 'success'])
     */
    public function badge(string $defaultColor = 'neutral', array $colors = []): static
    {
        $this->isBadge = true;
        $this->badgeColor = $defaultColor;
        $this->badgeColors = $colors;

        return $this;
    }

    public function format(string $formatter): static
    {
        $this->format = $formatter;

        return $this;
    }

    public function relation(string $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    /**
     * Render the cell as a published Blade view.
     * The view receives $row (the model/array) and $value (data_get result).
     *
     *   Column::make('Avatar', 'name')->view('tables.cells.avatar')
     */
    public function view(string $bladeName): static
    {
        $this->isHtml = true;
        $this->htmlView = $bladeName;

        return $this;
    }

    /**
     * Render the cell via a class implementing ColumnRenderer.
     *
     *   Column::make('Status', 'status')->html(StatusRenderer::class)
     */
    public function html(string $rendererClass): static
    {
        $this->isHtml = true;
        $this->htmlRenderer = $rendererClass;

        return $this;
    }

    /**
     * Mark the column value as trusted HTML and echo with {!! !!}.
     * Use only when the value is already escaped/sanitised upstream.
     *
     *   Column::make('Excerpt', 'excerpt_html')->raw()
     */
    public function raw(): static
    {
        $this->isRaw = true;

        return $this;
    }

    /**
     * Hide this column in the table until the given Tailwind breakpoint.
     * The column will still appear in the mobile card stack.
     *
     * @param  string  $breakpoint  'sm' | 'md' | 'lg' | 'xl' | '2xl'
     */
    public function visibleFrom(string $breakpoint): static
    {
        $this->visibleFrom = $breakpoint;

        return $this;
    }

    /**
     * Hide this column in the table on mobile (< md).
     * Shorthand for ->visibleFrom('md').
     * The column still appears in the mobile card stack.
     */
    public function hideOnMobile(): static
    {
        return $this->visibleFrom('md');
    }

    /**
     * Exclude this column from CSV/Excel exports.
     * Useful for action columns or columns with HTML-only content.
     */
    public function excludeFromExport(): static
    {
        $this->exportable = false;

        return $this;
    }

    /**
     * @param  array<int, Action>  $actions
     */
    public function actions(array $actions): static
    {
        $this->isActions = true;
        $this->actions = $actions;

        return $this;
    }

    /**
     * Resolve the badge color modifier for a given cell value.
     */
    public function resolveBadgeColor(mixed $value): string
    {
        $key = (string) $value;

        return $this->badgeColors[$key] ?? ($this->badgeColor ?? 'neutral');
    }

    /**
     * Get the resolved value for a given row (supports dot-notation relations).
     *
     * @param  array<string, mixed>|\Illuminate\Database\Eloquent\Model  $row
     */
    public function getValue(mixed $row): mixed
    {
        if ($this->key === null) {
            return null;
        }

        return data_get($row, $this->key);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'label'        => $this->label,
            'key'          => $this->key,
            'sortable'     => $this->sortable,
            'searchable'   => $this->searchable,
            'isBadge'      => $this->isBadge,
            'badgeColor'   => $this->badgeColor,
            'badgeColors'  => $this->badgeColors,
            'isActions'    => $this->isActions,
            'isRaw'        => $this->isRaw,
            'isHtml'       => $this->isHtml,
            'htmlView'     => $this->htmlView,
            'htmlRenderer' => $this->htmlRenderer,
            'format'       => $this->format,
            'relation'     => $this->relation,
            'exportable'   => $this->exportable,
            'visibleFrom'  => $this->visibleFrom,
            'actions'      => array_map(fn (Action $a): array => $a->toArray(), $this->actions),
        ];
    }
}
