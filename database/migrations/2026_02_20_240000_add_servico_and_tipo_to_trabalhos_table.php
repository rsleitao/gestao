<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabalhos', function (Blueprint $table) {
            $table->foreignId('id_servico')->nullable()->after('id_tecnico')->constrained('servicos')->nullOnDelete();
            $table->string('tipo_trabalho', 50)->nullable()->after('id_servico'); // licenciamento, execucao
        });
    }

    public function down(): void
    {
        Schema::table('trabalhos', function (Blueprint $table) {
            $table->dropForeign(['id_servico']);
            $table->dropColumn('tipo_trabalho');
        });
    }
};
