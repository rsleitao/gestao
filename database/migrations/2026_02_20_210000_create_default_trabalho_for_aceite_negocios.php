<?php

use App\Models\Negocio;
use App\Models\Trabalho;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Negócios já em Aceite ou Em trabalho sem trabalhos: criar 1 trabalho "A fazer"
     * para aparecerem no Kanban trabalhos.
     */
    public function up(): void
    {
        $negocios = Negocio::whereIn('status', ['aceite', 'em_trabalho'])
            ->whereDoesntHave('trabalhos')
            ->get();

        foreach ($negocios as $negocio) {
            Trabalho::create([
                'id_negocio' => $negocio->id,
                'designacao' => $negocio->designacao,
                'estado' => Trabalho::ESTADO_A_FAZER,
                'ordem' => 1,
            ]);
            if ($negocio->status === 'aceite') {
                $negocio->update(['status' => 'em_trabalho']);
            }
        }
    }

    public function down(): void
    {
        // Não remover trabalhos criados (poderiam ter sido alterados)
    }
};
