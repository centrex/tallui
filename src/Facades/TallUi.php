<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Centrex\TallUi\TallUi
 */
class TallUi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Centrex\TallUi\TallUi::class;
    }
}
