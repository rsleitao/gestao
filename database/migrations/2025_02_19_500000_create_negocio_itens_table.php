<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_negocio')->constrained('negocios')->cascadeOnDelete();
            $table->string('descricao');
            $table->decimal('preco', 10, 2); // Preço unitário
            $table->decimal('quantidade', 10, 2)->default(1); // Pode ser 1.5 horas, 2 unidades, etc.
            $table->integer('prazo_dias')->nullable(); // Prazo em dias para este item
            $table->integer('ordem')->default(0); // Para ordenar os itens
            $table->timestamps();

            $table->index('id_negocio');
            $table->index('ordem');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_itens');
    }
};
