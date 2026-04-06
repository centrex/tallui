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

        // Also expose as <x-tallui-icon name="heroicon-o-pencil" /> for use within package views
        Blade::component(\BladeUI\Icons\Components\Icon::class, 'tallui-icon');

        /** @var string $prefix */
        $prefix = config('tallui.prefix', 'tallui');

        Blade::component("{$prefix}-button", View\Components\Button::class);
        Blade::component("{$prefix}-input", View\Components\Form\Input::class);
        Blade::component("{$prefix}-form-group", View\Components\Form\FormGroup::class);
        Blade::component("{$prefix}-textarea", View\Components\Form\Textarea::class);
        Blade::component("{$prefix}-select", View\Components\Form\Select::class);
        Blade::component("{$prefix}-checkbox", View\Components\Form\Checkbox::class);
        Blade::component("{$prefix}-radio", View\Components\Form\Radio::class);
        Blade::component("{$prefix}-toggle", View\Components\Form\Toggle::class);
        Blade::component("{$prefix}-date-picker", View\Components\Form\DatePicker::class);
        Blade::component("{$prefix}-badge", View\Components\Badge::class);
        Blade::component("{$prefix}-toast", View\Components\ToastContainer::class);

        // Display & feedback
        Blade::component("{$prefix}-alert", View\Components\Alert::class);
        Blade::component("{$prefix}-avatar", View\Components\Avatar::class);
        Blade::component("{$prefix}-breadcrumb", View\Components\Breadcrumb::class);
        Blade::component("{$prefix}-loading", View\Components\Loading::class);
        Blade::component("{$prefix}-progress", View\Components\Progress::class);
        Blade::component("{$prefix}-rating", View\Components\Rating::class);
        Blade::component("{$prefix}-stat", View\Components\Stat::class);
        Blade::component("{$prefix}-steps", View\Components\Steps::class);
        Blade::component("{$prefix}-timeline", View\Components\Timeline::class);
        Blade::component("{$prefix}-error", View\Components\ErrorMessage::class);

        // Interactive / layout
        Blade::component("{$prefix}-accordion", View\Components\Accordion::class);
        Blade::component("{$prefix}-carousel", View\Components\Carousel::class);
        Blade::component("{$prefix}-drawer", View\Components\Drawer::class);
        Blade::component("{$prefix}-group", View\Components\Group::class);
        Blade::component("{$prefix}-image-gallery", View\Components\ImageGallery::class);
        Blade::component("{$prefix}-menu", View\Components\Menu::class);
        Blade::component("{$prefix}-popover", View\Components\Popover::class);
        Blade::component("{$prefix}-spotlight", View\Components\Spotlight::class);
        Blade::component("{$prefix}-swap", View\Components\Swap::class);
        Blade::component("{$prefix}-tab", View\Components\Tab::class);
        Blade::component("{$prefix}-tags", View\Components\Tags::class);
        Blade::component("{$prefix}-theme-toggle", View\Components\ThemeToggle::class);
        Blade::component("{$prefix}-calendar", View\Components\Calendar::class);

        // Form
        Blade::component("{$prefix}-file", View\Components\Form\File::class);
        Blade::component("{$prefix}-range", View\Components\Form\Range::class);
        Blade::component("{$prefix}-text-editor", View\Components\Form\TextEditor::class);
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
