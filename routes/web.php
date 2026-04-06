<?php

declare(strict_types = 1);

use Centrex\TallUi\Http\Controllers\SelectSearchController;
use Illuminate\Support\Facades\Route;

$prefix = config('tallui.route_prefix', '');

Route::get(
    ltrim($prefix . '/tallui/select-search', '/'),
    SelectSearchController::class,
)->name('tallui.select-search');
