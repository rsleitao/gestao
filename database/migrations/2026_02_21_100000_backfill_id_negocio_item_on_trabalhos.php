<?php

use App\Models\Trabalho;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Preenche id_negocio_item nos trabalhos que ainda não têm,
     * usando os itens do negócio (cada trabalho fica com um item por ordem).
     */
    public function up(): void
    {
        $trabalhosSemItem = Trabalho::whereNull('id_negocio_item')
            ->whereHas('negocio', fn ($q) => $q->whereHas('itens'))
            ->orderBy('id_negocio')
            ->orderBy('id')
            ->get();

        foreach ($trabalhosSemItem as $trabalho) {
            $negocio = $trabalho->negocio;
            $itens = $negocio->itens()->orderBy('ordem')->orderBy('id')->get();
            $idsItemJaUsados = Trabalho::where('id_negocio', $negocio->id)
                ->whereNotNull('id_negocio_item')
                ->pluck('id_negocio_item')
                ->toArray();

            foreach ($itens as $item) {
                if (in_array((int) $item->id, $idsItemJaUsados, true)) {
                    continue;
                }
                $trabalho->update(['id_negocio_item' => $item->id]);
                $idsItemJaUsados[] = (int) $item->id;
                break;
            }
        }
    }

    public function down(): void
    {
        // Não reverter - os ids preenchidos podem ser intencionais
    }
};
