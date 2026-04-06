<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Commands;

use Illuminate\Console\Command;

class TallUiBootcampCommand extends Command
{
    public $signature = 'tallui:list';

    public $description = 'List all TallUI registered components and their tags';

    public function handle(): int
    {
        /** @var string $prefix */
        $prefix = config('tallui.prefix', 'tallui');

        $this->components->info('TallUI — Registered Components');
        $this->newLine();

        $this->line('  <fg=yellow>Blade Components</> (prefix: <fg=cyan>' . $prefix . '</>)');
        $this->newLine();

        $bladeComponents = [
            // Form
            ['<x-' . $prefix . '-input />',        'Text / email / number / password field'],
            ['<x-' . $prefix . '-textarea />',     'Multi-line textarea'],
            ['<x-' . $prefix . '-select />',       'Static or async-searchable select'],
            ['<x-' . $prefix . '-checkbox />',     'Checkbox with label'],
            ['<x-' . $prefix . '-radio />',        'Radio button'],
            ['<x-' . $prefix . '-toggle />',       'Toggle switch'],
            ['<x-' . $prefix . '-range />',        'Range slider with optional value display'],
            ['<x-' . $prefix . '-file />',         'File / multi-file upload input'],
            ['<x-' . $prefix . '-date-picker />',  'Date / datetime-local picker'],
            ['<x-' . $prefix . '-text-editor />',  'Contenteditable rich text editor'],
            ['<x-' . $prefix . '-tags />',         'Tag input with add/remove'],
            ['<x-' . $prefix . '-form-group />',   'Label + helper/error wrapper'],
            ['<x-' . $prefix . '-error />',        'Inline validation error message'],
            // Display
            ['<x-' . $prefix . '-alert />',        'Dismissible info/success/warning/error alert'],
            ['<x-' . $prefix . '-avatar />',       'Avatar with image, initials, or placeholder'],
            ['<x-' . $prefix . '-badge />',        'Status badge'],
            ['<x-' . $prefix . '-breadcrumb />',   'Breadcrumb navigation trail'],
            ['<x-' . $prefix . '-loading />',      'Spinner / dots / ring / bars loading indicator'],
            ['<x-' . $prefix . '-progress />',     'Progress bar with optional label'],
            ['<x-' . $prefix . '-rating />',       'Star rating input or display'],
            ['<x-' . $prefix . '-stat />',         'KPI stat block with icon and change indicator'],
            ['<x-' . $prefix . '-steps />',        'Multi-step progress indicator'],
            ['<x-' . $prefix . '-timeline />',     'Vertical timeline of events'],
            // Interactive / layout
            ['<x-' . $prefix . '-accordion />',    'Collapsible accordion panel'],
            ['<x-' . $prefix . '-button />',       'Button with icon, color, and loading state'],
            ['<x-' . $prefix . '-calendar />',     'Month calendar with events and date selection'],
            ['<x-' . $prefix . '-carousel />',     'Auto-play image carousel with lightbox'],
            ['<x-' . $prefix . '-drawer />',       'Slide-in sidebar drawer'],
            ['<x-' . $prefix . '-group />',        'DaisyUI join wrapper for grouped elements'],
            ['<x-' . $prefix . '-image-gallery />', 'Responsive image grid with lightbox'],
            ['<x-' . $prefix . '-menu />',         'Sidebar/dropdown menu with nested children'],
            ['<x-' . $prefix . '-popover />',      'Hover or click-triggered popover tooltip'],
            ['<x-' . $prefix . '-spotlight />',    'Command-palette / global search overlay'],
            ['<x-' . $prefix . '-swap />',         'DaisyUI swap (rotate/flip between two states)'],
            ['<x-' . $prefix . '-tab />',          'Tabbed panel switcher'],
            ['<x-' . $prefix . '-theme-toggle />', 'Light/dark theme toggle with localStorage'],
            ['<x-' . $prefix . '-toast />',        'Toast notification container'],
        ];

        $this->table(['Tag', 'Description'], $bladeComponents);

        $this->newLine();
        $this->line('  <fg=yellow>Livewire Components</> (prefix: <fg=cyan>' . $prefix . '</>)');
        $this->newLine();

        $livewireComponents = [
            ['<livewire:' . $prefix . '-data-table />', 'Server-driven sortable/searchable/filterable table'],
            ['<livewire:' . $prefix . '-line-chart />',  'ApexCharts line chart with optional polling'],
            ['<livewire:' . $prefix . '-bar-chart />',   'ApexCharts bar / horizontal bar chart'],
            ['<livewire:' . $prefix . '-pie-chart />',   'ApexCharts pie or donut chart'],
            ['<livewire:' . $prefix . '-area-chart />',  'ApexCharts stacked or plain area chart'],
        ];

        $this->table(['Tag', 'Description'], $livewireComponents);

        $this->newLine();
        $this->line('  Config: <fg=gray>config/tallui.php</>   Prefix: <fg=cyan>' . $prefix . '</>');
        $this->line('  Publish: <fg=gray>php artisan tallui:install --config --views</>');
        $this->newLine();

        return self::SUCCESS;
    }
}
