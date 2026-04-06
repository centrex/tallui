<?php

declare(strict_types = 1);

namespace Centrex\TallUi;

use Centrex\TallUi\Commands\{TallUiBootcampCommand, TallUiInstallCommand};
use Centrex\TallUi\Livewire\Charts\{AreaChart, BarChart, LineChart, PieChart};
use Centrex\TallUi\Livewire\DataTable;
use Illuminate\Support\{Arr, ServiceProvider};
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class TallUiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tallui');
        $this->registerComponents();
        $this->registerLivewireComponents();
        $this->registerBladeDirectives();

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('tallui.php'),
            ], 'tallui-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/tallui'),
            ], 'tallui-views');

            $this->commands([TallUiInstallCommand::class, TallUiBootcampCommand::class]);
        }
    }

    public function registerComponents(): void
    {
        // Remap <x-icon> from BladeUI Icons to <x-svg> to avoid collision
        Blade::component(\BladeUI\Icons\Components\Icon::class, 'svg');

        /** @var string $prefix */
        $prefix = config('tallui.prefix', 'tallui');

        $this->loadViewComponentsAs($prefix, [
            // Existing
            View\Components\Button::class,

            // Form components
            View\Components\Form\FormGroup::class,
            View\Components\Form\Input::class,
            View\Components\Form\Textarea::class,
            View\Components\Form\Select::class,
            View\Components\Form\Checkbox::class,
            View\Components\Form\Radio::class,
            View\Components\Form\Toggle::class,
            View\Components\Form\DatePicker::class,
        ]);
    }

    public function registerLivewireComponents(): void
    {
        /** @var string $prefix */
        $prefix = config('tallui.prefix', 'tallui');

        Livewire::component("{$prefix}-data-table", DataTable::class);
        Livewire::component("{$prefix}-line-chart", LineChart::class);
        Livewire::component("{$prefix}-bar-chart", BarChart::class);
        Livewire::component("{$prefix}-pie-chart", PieChart::class);
        Livewire::component("{$prefix}-area-chart", AreaChart::class);
    }

    public function registerBladeDirectives(): void
    {
        $this->registerScopeDirective();
    }

    public function registerScopeDirective(): void
    {
        /**
         * All credits from this blade directive goes to Konrad Kalemba.
         * Just copied and modified for my very specific use case.
         *
         * https://github.com/konradkalemba/blade-components-scoped-slots
         */
        Blade::directive('scope', function ($expression): string {
            // Split the expression by `top-level` commas (not in parentheses)
            $directiveArguments = preg_split("/,(?![^\(\(]*[\)\)])/", $expression);
            $directiveArguments = array_map('trim', $directiveArguments);

            [$name, $functionArguments] = $directiveArguments;

            // Build function "uses" to inject extra external variables
            $uses = Arr::except(array_flip($directiveArguments), [$name, $functionArguments]);
            $uses = array_flip($uses);
            $uses[] = '$__env';
            $uses[] = '$__bladeCompiler';
            $uses = implode(',', $uses);

            /**
             *  Slot names can't contain dots, e.g. `user.city`.
             *  So we convert `user.city` to `user___city`
             *
             *  Later, on component it will be replaced back.
             */
            $name = str_replace('.', '___', $name);

            return "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$loop = null; \$__env->slot({$name}, function({$functionArguments}) use ({$uses}) { \$loop = (object) \$__env->getLoopStack()[0] ?>";
        });

        Blade::directive('endscope', fn (): string => '<?php }); ?>');
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'tallui');

        $this->app->singleton('tallui', fn (): TallUi => new TallUi());
    }
}
