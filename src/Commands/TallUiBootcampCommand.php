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
            ['<x-' . $prefix . '-input />',       'Text / email / number input field'],
            ['<x-' . $prefix . '-textarea />',     'Multi-line textarea'],
            ['<x-' . $prefix . '-select />',       'Static or async-searchable select'],
            ['<x-' . $prefix . '-checkbox />',     'Checkbox with label'],
            ['<x-' . $prefix . '-radio />',        'Radio button'],
            ['<x-' . $prefix . '-toggle />',       'Toggle switch'],
            ['<x-' . $prefix . '-date-picker />',  'Date / datetime-local picker'],
            ['<x-' . $prefix . '-form-group />',   'Label + helper/error wrapper'],
            ['<x-' . $prefix . '-button />',       'Button with icon and loading state'],
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
