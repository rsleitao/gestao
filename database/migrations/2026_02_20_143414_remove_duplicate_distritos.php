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
        // Encontrar todos os distritos duplicados
        $duplicados = DB::table('distritos')
            ->select('nome', DB::raw('MIN(id) as manter_id'), DB::raw('GROUP_CONCAT(id ORDER BY id) as todos_ids'))
            ->groupBy('nome')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicados as $dup) {
            $manterId = $dup->manter_id;
            $todosIds = explode(',', $dup->todos_ids);
            $idsParaRemover = array_filter($todosIds, fn($id) => $id != $manterId);

            // Atualizar referências em concelhos para apontar para o registro mantido
            foreach ($idsParaRemover as $idRemover) {
                DB::table('concelhos')
                    ->where('id_distrito', $idRemover)
                    ->update(['id_distrito' => $manterId]);
            }

            // Atualizar referências em imoveis (se existir a coluna)
            if (Schema::hasColumn('imoveis', 'id_distrito')) {
                foreach ($idsParaRemover as $idRemover) {
                    DB::table('imoveis')
                        ->where('id_distrito', $idRemover)
                        ->update(['id_distrito' => $manterId]);
                }
            }

            // Remover os registros duplicados
            DB::table('distritos')->whereIn('id', $idsParaRemover)->delete();
        }

        // Adicionar constraint UNIQUE no campo nome para prevenir futuros duplicados
        Schema::table('distritos', function (Blueprint $table) {
            $table->unique('nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover constraint UNIQUE
        Schema::table('distritos', function (Blueprint $table) {
            $table->dropUnique(['nome']);
        });
    }
};
