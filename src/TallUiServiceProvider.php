<?php

declare(strict_types = 1);

namespace Centrex\TallUi;

use Centrex\TallUi\Commands\{TallUiBootcampCommand, TallUiInstallCommand};
use Illuminate\Support\{Arr, ServiceProvider};
use Illuminate\Support\Facades\Blade;

class TallUiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->registerComponents();
        $this->registerBladeDirectives();

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('tallui.php'),
            ], 'tallui-config');

            // Registering package commands.
            $this->commands([TallUiInstallCommand::class, TallUiBootcampCommand::class]);
        }
    }

    public function registerComponents(): void
    {
        // Just rename <x-icon> provided by BladeUI Icons to <x-svg> to not collide with ours
        Blade::component(\BladeUI\Icons\Components\Icon::class, 'svg');

        // Register the Blade components
        $this->loadViewComponentsAs(config('tallui.prefix', 'tallui'), [
            View\Components\Button::class,
        ]);
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
             *  Slot names can`t contains dot , eg: `user.city`.
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
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'tallui');

        // Register the main class to use with the facade
        $this->app->singleton('tallui', fn (): TallUi => new TallUi());
    }
}
