<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrato;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class CompleteExpiredContracts extends Command
{
    protected $signature = 'contracts:complete-expired';
    protected $description = 'Mark expired contracts as rejected';

    public function handle()
    {
        $today = Carbon::today();
        $expiredContracts = Contrato::where('status', 'active')
                                    ->where('end_date', '<', $today)
                                    ->get();

        foreach ($expiredContracts as $contract) {
            $contract->update(['status' => 'rejected']);
            $this->info("Contrato ID {$contract->id} marcado como rechazado por expiración.");

            // Notificar al Admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'contrato_id' => $contract->id,
                    'message' => "El contrato para la zona {$contract->zona->name} (Turno: {$contract->type}) expiró el {$contract->end_date->toDateString()} y fue marcado como rechazado.",
                    'read' => false,
                ]);
            }
        }

        if ($expiredContracts->isEmpty()) {
            $this->info("No hay contratos expirados para procesar.");
        } else {
            $this->info("Se procesaron {$expiredContracts->count()} contratos expirados.");
        }
    }
}
