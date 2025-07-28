<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CodigoRegistro;
use Illuminate\Support\Str;

class GenerarCodigoRegistro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'codigo:generar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera un nuevo código de registro cada 30 minutos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $codigo = Str::upper(Str::random(8));
        CodigoRegistro::create([
            'codigo' => $codigo,
            'vigente_hasta' => now()->addMinutes(30),
        ]);
        $this->info("Código generado: $codigo");
    }
}
