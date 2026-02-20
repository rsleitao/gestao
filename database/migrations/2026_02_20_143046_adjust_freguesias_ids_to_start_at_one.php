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
        // Verificar se há dados relacionados em imóveis
        $temImoveisComFreguesia = DB::table('imoveis')->whereNotNull('id_freguesia')->exists();

        if ($temImoveisComFreguesia) {
            // Se houver imóveis, precisamos atualizar também as referências
            // Continuamos com o processo
        }

        // Remover foreign keys temporariamente
        Schema::table('imoveis', function (Blueprint $table) {
            $table->dropForeign(['id_freguesia']);
        });
        Schema::table('freguesias', function (Blueprint $table) {
            $table->dropForeign(['id_concelho']);
        });

        // Obter freguesias ordenadas por ID atual
        $freguesias = DB::table('freguesias')->orderBy('id')->get();
        $freguesiaMap = []; // mapeia ID antigo -> ID novo

        // Criar mapeamento: primeira freguesia passa a ID 1, segunda a ID 2, etc.
        $novoId = 1;
        foreach ($freguesias as $freguesia) {
            $freguesiaMap[$freguesia->id] = $novoId;
            $novoId++;
        }

        // Obter mapeamento de concelhos (caso os IDs dos concelhos tenham sido ajustados)
        $concelhos = DB::table('concelhos')->orderBy('id')->get();
        $concelhoMap = [];
        $novoConcelhoId = 1;
        foreach ($concelhos as $concelho) {
            $concelhoMap[$concelho->id] = $novoConcelhoId;
            $novoConcelhoId++;
        }

        // Atualizar id_concelho nas freguesias primeiro (caso os concelhos tenham sido re-sequenciados)
        foreach ($freguesias as $freguesia) {
            $novoConcelhoId = $concelhoMap[$freguesia->id_concelho] ?? $freguesia->id_concelho;
            if ($novoConcelhoId != $freguesia->id_concelho) {
                DB::table('freguesias')
                    ->where('id', $freguesia->id)
                    ->update(['id_concelho' => $novoConcelhoId]);
            }
        }

        // Ajustar IDs das freguesias primeiro (começar pelo maior para evitar conflitos)
        $freguesiasOrdenadas = $freguesias->sortByDesc('id');
        foreach ($freguesiasOrdenadas as $freguesia) {
            if ($freguesia->id != $freguesiaMap[$freguesia->id]) {
                DB::table('freguesias')
                    ->where('id', $freguesia->id)
                    ->update(['id' => $freguesiaMap[$freguesia->id] + 100000]); // Temporário para evitar conflitos
            }
        }
        foreach ($freguesiasOrdenadas as $freguesia) {
            DB::table('freguesias')
                ->where('id', $freguesiaMap[$freguesia->id] + 100000)
                ->update(['id' => $freguesiaMap[$freguesia->id]]);
        }

        // Atualizar id_freguesia nos imóveis após re-sequenciar as freguesias
        if ($temImoveisComFreguesia) {
            $imoveis = DB::table('imoveis')->whereNotNull('id_freguesia')->get();
            foreach ($imoveis as $imovel) {
                $novoFreguesiaId = $freguesiaMap[$imovel->id_freguesia] ?? null;
                if ($novoFreguesiaId && $novoFreguesiaId != $imovel->id_freguesia) {
                    DB::table('imoveis')
                        ->where('id', $imovel->id)
                        ->update(['id_freguesia' => $novoFreguesiaId]);
                }
            }
        }

        // Resetar auto_increment para freguesias
        $maxId = DB::table('freguesias')->max('id');
        DB::statement("ALTER TABLE freguesias AUTO_INCREMENT = " . ($maxId + 1));

        // Recriar foreign keys
        Schema::table('freguesias', function (Blueprint $table) {
            $table->foreign('id_concelho')->references('id')->on('concelhos')->cascadeOnDelete();
        });
        Schema::table('imoveis', function (Blueprint $table) {
            $table->foreign('id_freguesia')->references('id')->on('freguesias')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não podemos reverter com segurança sem saber os IDs originais
        // Esta migration é irreversível
    }
};
