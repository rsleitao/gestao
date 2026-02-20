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
        // Obter todos os concelhos ordenados por ID para criar o mapeamento
        // O id_concelho no SQL corresponde à ordem de inserção dos concelhos
        $concelhos = DB::table('concelhos')->orderBy('id')->get();
        $concelhosMap = [];
        foreach ($concelhos as $index => $concelho) {
            // O índice começa em 0, mas os IDs antigos começam em 1
            $concelhosMap[$index + 1] = $concelho->id;
        }

        // Carregar dados das freguesias do arquivo gerado
        $dataFile = base_path('freguesias_data.php');
        if (!file_exists($dataFile)) {
            throw new \Exception("Arquivo freguesias_data.php não encontrado em: {$dataFile}");
        }
        
        // Incluir o arquivo de dados
        require $dataFile;
        
        if (!isset($freguesiasData) || !is_array($freguesiasData)) {
            throw new \Exception("Variável \$freguesiasData não encontrada ou inválida no arquivo de dados");
        }

        // Processar e inserir freguesias em lotes
        $batch = [];
        $batchSize = 500;
        
        foreach ($freguesiasData as [$nome, $oldConcelhoId]) {
            if (isset($concelhosMap[$oldConcelhoId])) {
                $batch[] = [
                    'nome' => $nome,
                    'id_concelho' => $concelhosMap[$oldConcelhoId],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Inserir em lotes
                if (count($batch) >= $batchSize) {
                    DB::table('freguesias')->insertOrIgnore($batch);
                    $batch = [];
                }
            }
        }
        
        // Inserir o restante
        if (!empty($batch)) {
            DB::table('freguesias')->insertOrIgnore($batch);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não fazemos rollback automático pois pode haver dados relacionados
        // O rollback deve ser manual se necessário
    }
};
