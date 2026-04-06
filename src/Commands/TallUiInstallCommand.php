<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Commands;

use Illuminate\Console\Command;

class TallUiInstallCommand extends Command
{
    public $signature = 'tallui:install
                        {--config : Publish the config file}
                        {--views  : Publish the Blade view templates}
                        {--force  : Overwrite existing published files}';

    public $description = 'Install TallUI — publish config and (optionally) views';

    public function handle(): int
    {
        $this->components->info('Installing TallUI...');

        $this->publishConfig();

        if ($this->option('views')) {
            $this->publishViews();
        }

        $this->components->info('TallUI installed successfully.');
        $this->newLine();
        $this->line('  Add Tailwind CSS 4 + DaisyUI to your CSS:');
        $this->line('  <fg=gray>@import "tailwindcss";</>');
        $this->line('  <fg=gray>@plugin "daisyui";</>');
        $this->line('  <fg=gray>@source "../../vendor/centrex/tallui/resources/views";</>');
        $this->newLine();
        $this->line('  Or for Tailwind CSS 3, add to tailwind.config.js content[]:');
        $this->line('  <fg=gray>\'./vendor/centrex/tallui/resources/views/**/*.blade.php\'</>');
        $this->newLine();

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $params = ['--tag' => 'tallui-config'];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->callSilently('vendor:publish', $params);
        $this->components->task('Publishing config', fn () => true);
    }

    private function publishViews(): void
    {
        $params = ['--tag' => 'tallui-views'];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        $this->callSilently('vendor:publish', $params);
        $this->components->task('Publishing views', fn () => true);
    }
}
