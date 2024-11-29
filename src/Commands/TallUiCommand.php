<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Commands;

use Illuminate\Console\Command;

class TallUiCommand extends Command
{
    public $signature = 'tallui';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
