<?php

namespace App\Console\Commands;

use App\Enums\EntryStatus;
use App\Models\FinancialEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueEntries extends Command
{
    protected $signature = 'finance:check-overdue';

    protected $description = 'Atualiza títulos vencidos para overdue';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();

        $count = FinancialEntry::where('status', EntryStatus::Pending)
            ->where('due_date', '<', $today)
            ->update(['status' => EntryStatus::Overdue]);

        $this->info("{$count} registro(s) atualizado(s) para vencido");

        return Command::SUCCESS;
    }
}
