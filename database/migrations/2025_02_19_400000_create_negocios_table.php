<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocios', function (Blueprint $table) {
            $table->id();
            $table->string('status', 50)->default('pendente'); // pendente, aceite, cancelado, em_trabalho, concluido, faturado
            $table->foreignId('id_requerente')->constrained('requerentes')->cascadeOnDelete();
            $table->foreignId('id_imovel')->nullable()->constrained('imoveis')->nullOnDelete();
            $table->string('designacao');
            $table->text('observacoes')->nullable();
            $table->foreignId('id_processo')->nullable()->constrained('processos')->nullOnDelete();
            $table->foreignId('id_tecnico')->nullable()->constrained('users')->nullOnDelete(); // Técnico que criou o negócio
            $table->timestamp('data_convertido')->nullable(); // Quando foi aceite pelo cliente
            $table->timestamp('data_faturado')->nullable(); // Quando foi faturado
            $table->timestamps();

            $table->index('status');
            $table->index('id_requerente');
            $table->index('id_imovel');
            $table->index('id_processo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocios');
    }
};
