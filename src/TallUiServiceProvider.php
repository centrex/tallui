<?php

declare(strict_types = 1);

namespace Centrex\TallUi;

use Centrex\TallUi\Commands\{TallUiBootcampCommand, TallUiInstallCommand};
use Centrex\TallUi\Livewire\Charts\{AreaChart, BarChart, LineChart, MixedChart, PieChart, PolarAreaChart, RadarChart, RadialBarChart, RangeAreaChart, TreemapChart};
use Centrex\TallUi\Livewire\DataTable;
use Centrex\TallUi\Support\PackageVite;
use Illuminate\Support\{Arr, ServiceProvider};
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;

class TallUiServiceProvider extends ServiceProvider
{
    private static ?string $prefixCache = null;

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tallui');
        $this->registerViteDirective();
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

    private function registerViteDirective(): void
    {
        Blade::directive('talluiVite', fn (): string => sprintf(
            '<?php echo \\%s::render(%s, %s, %s); ?>',
            PackageVite::class,
            var_export(dirname(__DIR__), true),
            var_export('tallui.hot', true),
            var_export(['resources/js/app.js'], true),
        ));
    }

    public function registerComponents(): void
    {
        Blade::component(\BladeUI\Icons\Components\Icon::class, 'svg');

        // No matter if components has custom prefix or not,
        // we also register below alias to avoid naming collision,
        // because they are used inside some TallUi's components itself.
        Blade::component('tallui-button', View\Components\Button::class);
        Blade::component('tallui-card', View\Components\Card::class);
        Blade::component('tallui-icon', View\Components\Icon::class);
        Blade::component('tallui-input', View\Components\Form\Input::class);
        Blade::component('tallui-list-item', View\Components\ListItem::class);
        Blade::component('tallui-modal', View\Components\Modal::class);
        Blade::component('tallui-menu', View\Components\Menu::class);
        Blade::component('tallui-menu-item', View\Components\MenuItem::class);
        Blade::component('tallui-header', View\Components\Header::class);
        Blade::component('tallui-pagination', View\Components\Pagination::class);
        Blade::component('tallui-popover', View\Components\Popover::class);

        $prefix = self::$prefixCache ??= config('tallui.prefix', 'tallui');

        $components = [
            'icon'          => View\Components\Icon::class,
            'button'        => View\Components\Button::class,
            'input'         => View\Components\Form\Input::class,
            'form-group'    => View\Components\Form\FormGroup::class,
            'textarea'      => View\Components\Form\Textarea::class,
            'select'        => View\Components\Form\Select::class,
            'checkbox'      => View\Components\Form\Checkbox::class,
            'radio'         => View\Components\Form\Radio::class,
            'toggle'        => View\Components\Form\Toggle::class,
            'date-picker'   => View\Components\Form\DatePicker::class,
            'badge'         => View\Components\Badge::class,
            'toast'         => View\Components\ToastContainer::class,
            'alert'         => View\Components\Alert::class,
            'avatar'        => View\Components\Avatar::class,
            'breadcrumb'    => View\Components\Breadcrumb::class,
            'loading'       => View\Components\Loading::class,
            'progress'      => View\Components\Progress::class,
            'rating'        => View\Components\Rating::class,
            'stat'          => View\Components\Stat::class,
            'steps'         => View\Components\Steps::class,
            'timeline'      => View\Components\Timeline::class,
            'error'         => View\Components\ErrorMessage::class,
            'modal'         => View\Components\Modal::class,
            'accordion'     => View\Components\Accordion::class,
            'carousel'      => View\Components\Carousel::class,
            'drawer'        => View\Components\Drawer::class,
            'group'         => View\Components\Group::class,
            'image-gallery' => View\Components\ImageGallery::class,
            'menu'          => View\Components\Menu::class,
            'menu-item'     => View\Components\MenuItem::class,
            'list-item'     => View\Components\ListItem::class,
            'pagination'    => View\Components\Pagination::class,
            'popover'       => View\Components\Popover::class,
            'spotlight'     => View\Components\Spotlight::class,
            'swap'          => View\Components\Swap::class,
            'tab'           => View\Components\Tab::class,
            'tags'          => View\Components\Tags::class,
            'theme-toggle'  => View\Components\ThemeToggle::class,
            'calendar'      => View\Components\Calendar::class,
            'file'          => View\Components\Form\File::class,
            'range'         => View\Components\Form\Range::class,
            'text-editor'   => View\Components\Form\TextEditor::class,
            'card'          => View\Components\Card::class,
            'page-header'   => View\Components\PageHeader::class,
            'empty-state'   => View\Components\EmptyState::class,
            'notification'  => View\Components\Notification::class,
            // New components
            'sidebar'       => View\Components\Sidebar::class,
            'dialog'        => View\Components\Dialog::class,
            'collapse'      => View\Components\Collapse::class,
            'header'        => View\Components\Header::class,
            'image-library' => View\Components\ImageLibrary::class,
            'choices'       => View\Components\Form\Choices::class,
            'file-upload'   => View\Components\Form\FileUpload::class,
            'pin'           => View\Components\Form\Pin::class,
        ];

        foreach ($components as $name => $class) {
            Blade::component("{$prefix}-{$name}", $class);
        }
    }

    public function registerLivewireComponents(): void
    {
        $prefix = self::$prefixCache ??= config('tallui.prefix', 'tallui');

        Livewire::component("{$prefix}-data-table", DataTable::class);
        Livewire::component("{$prefix}-line-chart", LineChart::class);
        Livewire::component("{$prefix}-bar-chart", BarChart::class);
        Livewire::component("{$prefix}-pie-chart", PieChart::class);
        Livewire::component("{$prefix}-area-chart", AreaChart::class);
        Livewire::component("{$prefix}-mixed-chart", MixedChart::class);
        Livewire::component("{$prefix}-treemap-chart", TreemapChart::class);
        Livewire::component("{$prefix}-radial-bar-chart", RadialBarChart::class);
        Livewire::component("{$prefix}-radar-chart", RadarChart::class);
        Livewire::component("{$prefix}-polar-area-chart", PolarAreaChart::class);
        Livewire::component("{$prefix}-range-area-chart", RangeAreaChart::class);
    }

    public function registerBladeDirectives(): void
    {
        $this->registerScopeDirective();
        $this->registerPushOnceDirective();
        $this->registerMemoizeDirective();
        $this->registerLazyDirective();
        $this->registerStyleOnceDirective();
        $this->registerScriptOnceDirective();
    }

    public function registerScopeDirective(): void
    {
        Blade::directive('scope', function (string $expression): string {
            $directiveArguments = preg_split("/,(?![^\(\(]*[\)])]/", $expression);
            $directiveArguments = array_map('trim', $directiveArguments);

            [$name, $functionArguments] = $directiveArguments;

            $uses = Arr::except(array_flip($directiveArguments), [$name, $functionArguments]);
            $uses = array_flip($uses);
            $uses[] = '$__env';
            $uses[] = '$__bladeCompiler';
            $uses = implode(',', $uses);

            $name = str_replace('.', '___', $name);

            return "<?php \$__bladeCompiler = \$__bladeCompiler ?? null; \$loop = null; \$__env->slot({$name}, function({$functionArguments}) use ({$uses}) { \$loop = (object) \$__env->getLoopStack()[0] ?>";
        });

        Blade::directive('endscope', fn (): string => '<?php }); ?>');
    }

    /**
     * @pushonce(stack-name, 'unique-key)
     *   ...content pushed only the first time this key is encountered...
     *
     * @endpushonce
     *
     * Prevents duplicate JS/CSS assets when a component is used multiple times on a page.
     * More explicit than @once because it lets you target a named stack AND deduplicate by key.
     */
    public function registerPushOnceDirective(): void
    {
        Blade::directive('pushonce', function (string $expression): string {
            // expression: 'stack-name', 'key'  OR just 'stack-name' (key defaults to stack-name)
            $parts = array_map('trim', explode(',', $expression, 2));
            $stack = trim($parts[0], "'\"");
            $key = isset($parts[1]) ? trim($parts[1], "'\"") : $stack;

            return <<<PHP
                <?php if (!isset(\$__tallUiPushedOnce['{$key}'])) {
                    \$__tallUiPushedOnce['{$key}'] = true;
                    \$__env->startPush('{$stack}'); ?>
                PHP;
        });

        Blade::directive('endpushonce', fn (): string => '<?php $__env->stopPush(); } ?>');
    }

    /**
     * @memoize(cache-key)
     *   ...expensive Blade output rendered only once per request, then replayed from memory...
     *
     * @endmemoize
     *
     * Caches the rendered HTML string in a static array for the duration of the PHP request.
     * Ideal for repeated sub-views (e.g. nav items, icon sets) rendered inside loops.
     */
    public function registerMemoizeDirective(): void
    {
        Blade::directive('memoize', function (string $expression): string {
            $key = trim($expression, "'\"");

            return <<<PHP
                <?php
                if (!isset(\$__tallUiMemoCache['{$key}'])) {
                    ob_start();
                ?>
                PHP;
        });

        Blade::directive('endmemoize', function (string $expression): string {
            $key = trim($expression, "'\"");

            return <<<PHP
                <?php
                    \$__tallUiMemoCache['{$key}'] = ob_get_clean();
                }
                echo \$__tallUiMemoCache['{$key}'];
                ?>
                PHP;
        });
    }

    /**
     * @lazy(threshold=200px, rootMargin=0px)
     *   ...content that is only rendered when near the viewport (IntersectionObserver)...
     *
     * @endlazy
     *
     * Wraps the content in an Alpine.js x-intersect wrapper so the DOM is created upfront
     * but Alpine components inside initialise only when visible — reducing initial JS work.
     *
     * Requires Alpine x-intersect plugin (already bundled with Livewire 3 / Alpine 3.x).
     */
    public function registerLazyDirective(): void
    {
        Blade::directive('lazy', function (string $expression): string {
            $threshold = '0.1';
            $rootMargin = '200px';

            if ($expression) {
                $args = array_map('trim', explode(',', $expression, 2));
                $threshold = trim($args[0] ?? $threshold, "'\"");
                $rootMargin = trim($args[1] ?? $rootMargin, "'\"");
            }

            return <<<HTML
                <?php echo '<div x-data="{ visible: false }" x-intersect.threshold.{$threshold}="visible = true" style="min-height:1px">
                    <template x-if="visible">'; ?>
                HTML;
        });

        Blade::directive('endlazy', fn (): string => "<?php echo '</template></div>'; ?>");
    }

    /**
     * @styleonce(unique-key)
     *   <style>...</style>
     *
     * @endstyleonce
     *
     * Injects an inline <style> block into the `styles` stack exactly once,
     * regardless of how many times the surrounding component is rendered.
     * Equivalent to @pushonce(styles, key) but reads more clearly in component templates.
     */
    public function registerStyleOnceDirective(): void
    {
        Blade::directive('styleonce', function (string $expression): string {
            $key = trim($expression, "'\"");

            return <<<PHP
                <?php if (!isset(\$__tallUiPushedOnce['style:{$key}'])) {
                    \$__tallUiPushedOnce['style:{$key}'] = true;
                    \$__env->startPush('styles'); ?>
                PHP;
        });

        Blade::directive('endstyleonce', fn (): string => '<?php $__env->stopPush(); } ?>');
    }

    /**
     * @scriptonce(unique-key)
     *   <script>...</script>
     *
     * @endscriptonce
     *
     * Injects an inline <script> block into the `scripts` stack exactly once.
     * Prevents duplicate inline initialisation scripts when a component is looped.
     */
    public function registerScriptOnceDirective(): void
    {
        Blade::directive('scriptonce', function (string $expression): string {
            $key = trim($expression, "'\"");

            return <<<PHP
                <?php if (!isset(\$__tallUiPushedOnce['script:{$key}'])) {
                    \$__tallUiPushedOnce['script:{$key}'] = true;
                    \$__env->startPush('scripts'); ?>
                PHP;
        });

        Blade::directive('endscriptonce', fn (): string => '<?php $__env->stopPush(); } ?>');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'tallui');

        $this->app->singleton('tallui', fn (): TallUi => new TallUi());
    }
}
