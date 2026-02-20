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
        // Verificar se há dados relacionados
        $temImoveis = DB::table('imoveis')->whereNotNull('id_distrito')->orWhereNotNull('id_concelho')->exists();
        $temFreguesias = DB::table('freguesias')->exists();

        if ($temImoveis || $temFreguesias) {
            throw new \Exception('Não é possível ajustar os IDs: existem dados relacionados (imóveis ou freguesias).');
        }

        // Remover foreign keys temporariamente
        Schema::table('imoveis', function (Blueprint $table) {
            $table->dropForeign(['id_distrito']);
            $table->dropForeign(['id_concelho']);
        });
        Schema::table('freguesias', function (Blueprint $table) {
            $table->dropForeign(['id_concelho']);
        });
        Schema::table('concelhos', function (Blueprint $table) {
            $table->dropForeign(['id_distrito']);
        });

        // Obter distritos ordenados por ID atual
        $distritos = DB::table('distritos')->orderBy('id')->get();
        $distritoMap = []; // mapeia ID antigo -> ID novo

        // Criar mapeamento: primeiro distrito passa a ID 1, segundo a ID 2, etc.
        $novoId = 1;
        foreach ($distritos as $distrito) {
            $distritoMap[$distrito->id] = $novoId;
            $novoId++;
        }

        // Ajustar IDs dos distritos (começar pelo maior para evitar conflitos)
        $distritosOrdenados = $distritos->sortByDesc('id');
        foreach ($distritosOrdenados as $distrito) {
            if ($distrito->id != $distritoMap[$distrito->id]) {
                DB::table('distritos')
                    ->where('id', $distrito->id)
                    ->update(['id' => $distritoMap[$distrito->id] + 1000]); // Temporário para evitar conflitos
            }
        }
        foreach ($distritosOrdenados as $distrito) {
            DB::table('distritos')
                ->where('id', $distritoMap[$distrito->id] + 1000)
                ->update(['id' => $distritoMap[$distrito->id]]);
        }

        // Resetar auto_increment para distritos
        $maxId = DB::table('distritos')->max('id');
        DB::statement("ALTER TABLE distritos AUTO_INCREMENT = " . ($maxId + 1));

        // Obter concelhos ordenados por ID atual
        $concelhos = DB::table('concelhos')->orderBy('id')->get();
        $concelhoMap = []; // mapeia ID antigo -> ID novo

        // Criar mapeamento para concelhos
        $novoId = 1;
        foreach ($concelhos as $concelho) {
            $concelhoMap[$concelho->id] = $novoId;
            $novoId++;
        }

        // Ajustar id_distrito nos concelhos primeiro
        foreach ($concelhos as $concelho) {
            $novoDistritoId = $distritoMap[$concelho->id_distrito] ?? $concelho->id_distrito;
            DB::table('concelhos')
                ->where('id', $concelho->id)
                ->update(['id_distrito' => $novoDistritoId]);
        }

        // Ajustar IDs dos concelhos (começar pelo maior)
        $concelhosOrdenados = $concelhos->sortByDesc('id');
        foreach ($concelhosOrdenados as $concelho) {
            if ($concelho->id != $concelhoMap[$concelho->id]) {
                DB::table('concelhos')
                    ->where('id', $concelho->id)
                    ->update(['id' => $concelhoMap[$concelho->id] + 10000]); // Temporário
            }
        }
        foreach ($concelhosOrdenados as $concelho) {
            DB::table('concelhos')
                ->where('id', $concelhoMap[$concelho->id] + 10000)
                ->update(['id' => $concelhoMap[$concelho->id]]);
        }

        // Resetar auto_increment para concelhos
        $maxId = DB::table('concelhos')->max('id');
        DB::statement("ALTER TABLE concelhos AUTO_INCREMENT = " . ($maxId + 1));

        // Recriar foreign keys
        Schema::table('concelhos', function (Blueprint $table) {
            $table->foreign('id_distrito')->references('id')->on('distritos')->cascadeOnDelete();
        });
        Schema::table('freguesias', function (Blueprint $table) {
            $table->foreign('id_concelho')->references('id')->on('concelhos')->cascadeOnDelete();
        });
        Schema::table('imoveis', function (Blueprint $table) {
            $table->foreign('id_distrito')->references('id')->on('distritos')->nullOnDelete();
            $table->foreign('id_concelho')->references('id')->on('concelhos')->nullOnDelete();
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
