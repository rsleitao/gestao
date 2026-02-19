<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->string('referencia', 50)->unique();
            $table->foreignId('requerente_id')->constrained('requerentes')->cascadeOnDelete();
            $table->foreignId('servico_id')->nullable()->constrained('servicos')->nullOnDelete();
            $table->string('estado', 50)->default('aberto'); // aberto, em_analise, concluido, arquivado
            $table->date('data_abertura');
            $table->date('data_limite')->nullable();
            $table->date('data_conclusao')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['estado', 'data_abertura']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processos');
    }
};
