<?php

declare(strict_types = 1);

// ── Sanity ────────────────────────────────────────────────────────────────────

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'print_r'])
    ->each->not->toBeUsed();

// ── Namespace conventions ─────────────────────────────────────────────────────

it('has all src classes in the Centrex\\TallUi namespace')
    ->expect('Centrex\\TallUi')
    ->toUseStrictTypes();

// ── Contracts ─────────────────────────────────────────────────────────────────

it('contracts are interfaces')
    ->expect('Centrex\\TallUi\\Contracts')
    ->toBeInterfaces();

// ── Concerns / traits ─────────────────────────────────────────────────────────

it('Concerns are traits')
    ->expect('Centrex\\TallUi\\Concerns')
    ->toBeTraits();

// ── Livewire components ───────────────────────────────────────────────────────

it('Livewire components extend Livewire\\Component')
    ->expect('Centrex\\TallUi\\Livewire')
    ->toExtend(\Livewire\Component::class);

// ── Blade components ──────────────────────────────────────────────────────────

it('View components extend Illuminate\\View\\Component')
    ->expect('Centrex\\TallUi\\View\\Components')
    ->toExtend(\Illuminate\View\Component::class);

// ── DataTable value objects are final-safe (no inheritance of Action/Filter) ──

it('DataTable Column is not abstract')
    ->expect(\Centrex\TallUi\DataTable\Column::class)
    ->not->toBeAbstract();

it('DataTable Action is not abstract')
    ->expect(\Centrex\TallUi\DataTable\Action::class)
    ->not->toBeAbstract();

it('DataTable Filter is not abstract')
    ->expect(\Centrex\TallUi\DataTable\Filter::class)
    ->not->toBeAbstract();

// ── Chart components extend BaseChart ─────────────────────────────────────────

it('concrete chart components extend BaseChart')
    ->expect([
        \Centrex\TallUi\Livewire\Charts\LineChart::class,
        \Centrex\TallUi\Livewire\Charts\BarChart::class,
        \Centrex\TallUi\Livewire\Charts\PieChart::class,
        \Centrex\TallUi\Livewire\Charts\AreaChart::class,
    ])
    ->toExtend(\Centrex\TallUi\Livewire\Charts\BaseChart::class);

// ── ColumnRenderer implementations ────────────────────────────────────────────

it('BaseChart is abstract')
    ->expect(\Centrex\TallUi\Livewire\Charts\BaseChart::class)
    ->toBeAbstract();

// ── No globals / superglobals used ───────────────────────────────────────────

it('does not use $_GET or $_POST directly')
    ->expect('Centrex\\TallUi')
    ->not->toUse(['$_GET', '$_POST', '$_REQUEST', '$_SERVER']);

// ── Service provider ─────────────────────────────────────────────────────────

it('service provider extends Laravel ServiceProvider')
    ->expect(\Centrex\TallUi\TallUiServiceProvider::class)
    ->toExtend(\Illuminate\Support\ServiceProvider::class);

// ── HTTP controllers use the routing controller base ─────────────────────────

it('HTTP controllers extend Illuminate routing Controller')
    ->expect('Centrex\\TallUi\\Http\\Controllers')
    ->toExtend(\Illuminate\Routing\Controller::class);
