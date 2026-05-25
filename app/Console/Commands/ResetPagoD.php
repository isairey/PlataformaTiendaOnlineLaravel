<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuarios;

class ResetPagoD extends Command
{
    protected $signature = 'usuarios:resetPagoD';
    protected $description = 'Resetear pagoD a 0 para todos los usuarios con idrol 2';

    public function handle()
    {
        Usuarios::where('idrol', 2)->update(['pagoD' => 0]);
        $this->info('Se ha reseteado el pagoD a 0 para todos los usuarios con idrol 2.');
    }
}
