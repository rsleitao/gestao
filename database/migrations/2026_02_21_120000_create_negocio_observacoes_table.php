<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_observacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_negocio')->constrained('negocios')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            $table->text('observacao');
            $table->timestamps();

            $table->index('id_negocio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_observacoes');
    }
};
