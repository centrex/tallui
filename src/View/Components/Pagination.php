<?php

declare(strict_types = 1);

namespace Centrex\TallUi\View\Components;

use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pagination extends Component
{
    public function __construct(
        public LengthAwarePaginator $paginator,
        public string $size = '',           // xs | sm | md (default) | lg
        public bool $showInfo = true,     // "Showing X–Y of Z"
        public bool $showPerPage = false, // per-page selector
        public array $perPageOptions = [10, 25, 50, 100],
        public string $align = 'center',   // start | center | end
    ) {}

    public function render(): View|Closure|string
    {
        $pg = $this->paginator;
        $total = $pg->total();
        $perPage = $pg->perPage();
        $firstItem = $pg->firstItem() ?? 0;
        $lastItem = $pg->lastItem() ?? 0;
        $lastPage = $pg->lastPage();
        $current = $pg->currentPage();

        // Build a sliding window: always show first, last, current±2, with ellipsis
        $window = [];
        $gap = '…';

        if ($lastPage <= 7) {
            $window = range(1, $lastPage);
        } else {
            $left = max(2, $current - 2);
            $right = min($lastPage - 1, $current + 2);

            $window[] = 1;

            if ($left > 2) {
                $window[] = $gap;
            }

            foreach (range($left, $right) as $p) {
                $window[] = $p;
            }

            if ($right < $lastPage - 1) {
                $window[] = $gap;
            }
            $window[] = $lastPage;
        }

        return view('tallui::components.pagination', [
            'pg'        => $pg,
            'total'     => $total,
            'firstItem' => $firstItem,
            'lastItem'  => $lastItem,
            'lastPage'  => $lastPage,
            'current'   => $current,
            'window'    => $window,
            'gap'       => $gap,
        ]);
    }
}
