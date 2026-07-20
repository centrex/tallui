<?php

declare(strict_types = 1);

namespace Centrex\TallUi\DataTable;

use Illuminate\Support\Facades\Gate;

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

    /**
     * Named format hint applied to the cell value, e.g. 'currency', 'currency:EUR',
     * 'number', 'decimal:2', 'percent', 'date', 'datetime'.
     */
    public ?string $format = null;

    /** Text alignment: 'left' | 'center' | 'right'. Auto-inferred from $format when unset. */
    public ?string $align = null;

    /** Include this column in the footer totals row (numeric formats only). */
    public bool $summable = false;

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

    /** Ability name(s) checked via Gate::allows() before this column is included at all. */
    public string|array|null $ability = null;

    public mixed $abilityArguments = null;

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
     * Only include this column when Gate::allows($ability, $arguments) passes for the
     * current user. Unlike ->visibleFrom() (responsive) or the user-facing column-visibility
     * toggle, a denied column is dropped by the DataTable before columnDefs/query/export are
     * built — the data never reaches the client, not just hidden client-side.
     *
     *   Column::make('Cost Price', 'cost_price')->can('inventory.pricing.view')
     *   Column::make('Approve', )->actions([...])->can('orders.approve', $someModel)
     */
    public function can(string|array $ability, mixed $arguments = null): static
    {
        $this->ability = $ability;
        $this->abilityArguments = $arguments;

        return $this;
    }

    /** Whether the current user passes this column's ->can() gate (always true when unset). */
    public function isAuthorized(): bool
    {
        if ($this->ability === null) {
            return true;
        }

        return Gate::allows($this->ability, $this->abilityArguments);
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

    /**
     * @param  string  $formatter  'currency' | 'currency:EUR' | 'number' | 'decimal:2' | 'percent' | 'date' | 'datetime'
     */
    public function format(string $formatter): static
    {
        $this->format = $formatter;

        return $this;
    }

    /**
     * Shorthand for ->format('currency:CODE').
     */
    public function currency(string $currencyCode = 'USD'): static
    {
        $this->format = 'currency:' . $currencyCode;

        return $this;
    }

    public function align(string $alignment): static
    {
        $this->align = $alignment;

        return $this;
    }

    /**
     * Include this column in the footer totals row. The sum is computed across
     * every matching row (not just the current page), cached alongside the query.
     */
    public function summable(): static
    {
        $this->summable = true;

        return $this;
    }

    /**
     * Resolved text alignment: explicit ->align() wins, otherwise numeric
     * formats (currency/number/decimal/percent) default to right-aligned —
     * the convention financial tables use so digits line up on the ones place.
     */
    public function resolvedAlign(): string
    {
        if ($this->align !== null) {
            return $this->align;
        }

        return $this->isNumericFormat() ? 'right' : 'left';
    }

    public function isNumericFormat(): bool
    {
        if ($this->format === null) {
            return false;
        }

        $base = explode(':', $this->format, 2)[0];

        return in_array($base, ['currency', 'number', 'decimal', 'percent'], true);
    }

    /**
     * Format a raw value per this column's $format hint. Falls back to the
     * value cast to string when no format (or an unrecognised one) is set.
     */
    public function formatValue(mixed $value): string
    {
        return self::formatWithHint($value, $this->format);
    }

    /**
     * Static formatter usable from array-based column definitions (e.g. the
     * DataTable Blade view, which only has $columnDefs, not Column instances).
     */
    public static function formatWithHint(mixed $value, ?string $format): string
    {
        if ($format === null || $value === null || $value === '') {
            return (string) ($value ?? '');
        }

        [$base, $arg] = array_pad(explode(':', $format, 2), 2, null);

        return match ($base) {
            'currency' => self::formatCurrency((float) $value, $arg ?? 'USD'),
            'number'   => number_format((float) $value),
            'decimal'  => number_format((float) $value, (int) ($arg ?? 2)),
            'percent'  => number_format((float) $value * ($arg === 'raw' ? 1 : 100), 1) . '%',
            'date'     => self::formatDate($value, 'M j, Y'),
            'datetime' => self::formatDate($value, 'M j, Y g:i A'),
            default    => (string) $value,
        };
    }

    private static function formatCurrency(float $value, string $currencyCode): string
    {
        $formatted = number_format(abs($value), 2);
        $symbol = match (strtoupper($currencyCode)) {
            'USD'   => '$',
            'EUR'   => '€',
            'GBP'   => '£',
            'BDT'   => '৳',
            'JPY'   => '¥',
            'INR'   => '₹',
            default => strtoupper($currencyCode) . ' ',
        };

        // Accounting convention: negative amounts in parentheses, not a leading minus.
        return $value < 0 ? "({$symbol}{$formatted})" : "{$symbol}{$formatted}";
    }

    private static function formatDate(mixed $value, string $phpFormat): string
    {
        try {
            return ($value instanceof \DateTimeInterface ? $value : new \DateTimeImmutable((string) $value))
                ->format($phpFormat);
        } catch (\Exception) {
            return (string) $value;
        }
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
        return $this->badgeColors[self::scalarValue($value)] ?? ($this->badgeColor ?? 'neutral');
    }

    /**
     * Normalize a raw cell value to a plain string. Backed enums (e.g. Eloquent
     * enum casts) resolve to their backing value, unit enums to their case name —
     * a plain (string) cast on an enum is a fatal error.
     */
    public static function scalarValue(mixed $value): string
    {
        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        return (string) ($value ?? '');
    }

    /**
     * Human-readable badge text: snake_case values render as words
     * ('partially_settled' → 'Partially settled'); anything else only gets
     * its first letter capitalised, so acronyms like 'VIP' pass through.
     */
    public static function badgeLabel(mixed $value): string
    {
        return ucfirst(str_replace('_', ' ', self::scalarValue($value)));
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
            'align'        => $this->resolvedAlign(),
            'isNumeric'    => $this->isNumericFormat(),
            'summable'     => $this->summable,
            'relation'     => $this->relation,
            'exportable'   => $this->exportable,
            'visibleFrom'  => $this->visibleFrom,
            'actions'      => array_map(fn (Action $a): array => $a->toArray(), $this->actions),
        ];
    }
}
