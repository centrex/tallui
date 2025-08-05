<?php

declare(strict_types = 1);

namespace Centrex\TallUi\Commands;

use Illuminate\Console\Command;

class TallUiBootcampCommand extends Command
{
    public $signature = 'tallui-bootcamp';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
