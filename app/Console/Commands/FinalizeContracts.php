<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrato;
use Carbon\Carbon;

class FinalizeContracts extends Command
{
    protected $signature = 'contracts:finalize';
    protected $description = 'Finaliza contratos cuya fecha de fin ha pasado';

    public function handle()
    {
        $today = Carbon::today();
        $contratos = Contrato::where('status', 'active')
            ->where('end_date', '<', $today)
            ->get();

        foreach ($contratos as $contrato) {
            $contrato->update(['status' => 'finished']);
            $this->info("Contrato {$contrato->id} finalizado.");
        }
    }
}
