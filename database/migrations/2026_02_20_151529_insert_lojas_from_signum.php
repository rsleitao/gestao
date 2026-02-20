<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $nomes = [
            'Aldi',
            'Bombas',
            'CELIUMPROJ',
            'Continente',
            'Intermarché',
            'JD Sports',
            'KFC',
            'PizzaHut',
            'JYSK',
            'Volare',
            'KIWOKO',
            'Tiendanimal',
            'McDonalds',
            'Outros',
            'Pingo Doce',
            'Sacramento Campos',
            'Burger King',
            'PAC',
            'Bricomarché',
            'PCVE',
            'Grupo Mosqueteiros',
            'Taco Bell',
        ];

        $now = now();

        foreach ($nomes as $nome) {
            if (DB::table('lojas')->where('nome', $nome)->exists()) {
                continue;
            }
            DB::table('lojas')->insert([
                'nome' => $nome,
                'ativo' => true,
                'imagem' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     * Não remove lojas no rollback para não apagar lojas que já existiam (ex.: Intermarché).
     */
    public function down(): void
    {
        // Intencionalmente vazio
    }
};
