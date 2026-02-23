<?php

use App\Models\Negocio;
use App\Models\Trabalho;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Cria trabalhos em falta: um trabalho por item em cada negócio (aceite ou em trabalho).
     * Assim o Kanban lista todos os trabalhos (um card por item).
     */
    public function up(): void
    {
        $negocios = Negocio::whereIn('status', ['aceite', 'em_trabalho'])
            ->with(['itens' => fn ($q) => $q->orderBy('ordem')->orderBy('id')])
            ->get();

        foreach ($negocios as $negocio) {
            $itens = $negocio->itens;
            if ($itens->isEmpty()) {
                continue;
            }

            $idsItemComTrabalho = Trabalho::where('id_negocio', $negocio->id)
                ->whereNotNull('id_negocio_item')
                ->pluck('id_negocio_item')
                ->toArray();

            $ordem = (int) Trabalho::where('id_negocio', $negocio->id)->max('ordem');

            foreach ($itens as $item) {
                if (in_array((int) $item->id, $idsItemComTrabalho, true)) {
                    continue;
                }
                $ordem++;
                Trabalho::create([
                    'id_negocio' => $negocio->id,
                    'id_negocio_item' => $item->id,
                    'estado' => Trabalho::ESTADO_A_FAZER,
                    'ordem' => $ordem,
                ]);
                $idsItemComTrabalho[] = (int) $item->id;
            }
        }
    }

    public function down(): void
    {
        // Não remover trabalhos criados
    }
};
