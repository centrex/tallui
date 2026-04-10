<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Traits;

use Illuminate\Support\Facades\{Blade, Cache};

trait Toast
{
    private static array $iconCache = [];

    public function toast(
        string $type,
        string $title,
        ?string $description = null,
        ?string $position = null,
        string $icon = 'o-information-circle',
        string $css = 'alert-info',
        int $timeout = 3000,
        ?string $redirectTo = null,
    ) {
        $toast = [
            'type'        => $type,
            'title'       => $title,
            'description' => $description,
            'position'    => $position,
            'icon'        => $this->renderIconCached($icon),
            'css'         => $css,
            'timeout'     => $timeout,
        ];

        $this->js('toast(' . json_encode(['toast' => $toast]) . ')');

        session()->flash('tallui.toast.title', $title);
        session()->flash('tallui.toast.description', $description);

        if ($redirectTo) {
            return $this->redirect($redirectTo, navigate: true);
        }

        return null;
    }

    private function renderIconCached(string $icon): string
    {
        if (isset(self::$iconCache[$icon])) {
            return self::$iconCache[$icon];
        }

        $cacheEnabled = (bool) config('tallui.cache.store', false);

        if ($cacheEnabled) {
            $key = 'tallui:icon:' . $icon;
            self::$iconCache[$icon] = Cache::store(config('tallui.cache.store'))
                ->rememberForever($key, fn (): string => Blade::render(
                    "<x-tallui-icon class='w-7 h-7' name='" . $icon . "' />",
                ));
        } elseif (!isset(self::$iconCache[$icon])) {
            self::$iconCache[$icon] = Blade::render(
                "<x-tallui-icon class='w-7 h-7' name='" . $icon . "' />",
            );
        }

        return self::$iconCache[$icon];
    }

    public function success(
        string $title,
        ?string $description = null,
        ?string $position = null,
        string $icon = 'o-check-circle',
        string $css = 'alert-success',
        int $timeout = 3000,
        ?string $redirectTo = null,
    ) {
        return $this->toast('success', $title, $description, $position, $icon, $css, $timeout, $redirectTo);
    }

    public function warning(
        string $title,
        ?string $description = null,
        ?string $position = null,
        string $icon = 'o-exclamation-triangle',
        string $css = 'alert-warning',
        int $timeout = 3000,
        ?string $redirectTo = null,
    ) {
        return $this->toast('warning', $title, $description, $position, $icon, $css, $timeout, $redirectTo);
    }

    public function error(
        string $title,
        ?string $description = null,
        ?string $position = null,
        string $icon = 'o-x-circle',
        string $css = 'alert-error',
        int $timeout = 3000,
        ?string $redirectTo = null,
    ) {
        return $this->toast('error', $title, $description, $position, $icon, $css, $timeout, $redirectTo);
    }

    public function info(
        string $title,
        ?string $description = null,
        ?string $position = null,
        string $icon = 'o-information-circle',
        string $css = 'alert-info',
        int $timeout = 3000,
        ?string $redirectTo = null,
    ) {
        return $this->toast('info', $title, $description, $position, $icon, $css, $timeout, $redirectTo);
    }
}
