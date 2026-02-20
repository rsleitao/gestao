<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $distritos = [
            'Aveiro',
            'Beja',
            'Braga',
            'Bragança',
            'Castelo Branco',
            'Coimbra',
            'Évora',
            'Faro',
            'Guarda',
            'Leiria',
            'Lisboa',
            'Portalegre',
            'Porto',
            'Santarém',
            'Setúbal',
            'Viana do Castelo',
            'Vila Real',
            'Viseu',
            'Angra do Heroísmo',
            'Horta',
            'Ponta Delgada',
            'Funchal',
        ];

        foreach ($distritos as $nome) {
            DB::table('distritos')->insertOrIgnore([
                'nome' => $nome,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $distritos = [
            'Aveiro',
            'Beja',
            'Braga',
            'Bragança',
            'Castelo Branco',
            'Coimbra',
            'Évora',
            'Faro',
            'Guarda',
            'Leiria',
            'Lisboa',
            'Portalegre',
            'Porto',
            'Santarém',
            'Setúbal',
            'Viana do Castelo',
            'Vila Real',
            'Viseu',
            'Angra do Heroísmo',
            'Horta',
            'Ponta Delgada',
            'Funchal',
        ];

        DB::table('distritos')->whereIn('nome', $distritos)->delete();
    }
};
