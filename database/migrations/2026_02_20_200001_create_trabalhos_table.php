<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trabalhos = itens individuais de um negócio (pacote). Cada técnico faz um trabalho.
     * Estado: A fazer, Em execução, Pendente, Concluído.
     */
    public function up(): void
    {
        Schema::create('trabalhos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_negocio')->constrained('negocios')->cascadeOnDelete();
            $table->unsignedBigInteger('id_tecnico')->nullable();
            $table->string('designacao')->nullable();
            $table->string('estado', 30)->default('a_fazer');
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();

            $table->foreign('id_tecnico')->references('id')->on('users')->nullOnDelete();
            $table->index('estado');
            $table->index('id_negocio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabalhos');
    }
};
