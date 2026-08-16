<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\Clock;
use Illuminate\Console\Command;

class ShowClockCommand extends Command
{
    protected $signature = 'show-clock';

    protected $description = "Affiche l'instant fourni par l'implémentation de Clock résolue par le conteneur";

    public function handle(Clock $clock): int
    {
        $this->info($clock->now()->format(DATE_ATOM));
        $this->comment($clock::class);

        return self::SUCCESS;
    }
}
