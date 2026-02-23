<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trabalho herda descrição, tipo e prazo do item do negócio (negocio_item).
     */
    public function up(): void
    {
        Schema::table('trabalhos', function (Blueprint $table) {
            $table->foreignId('id_negocio_item')->nullable()->after('id_negocio')->constrained('negocio_itens')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trabalhos', function (Blueprint $table) {
            $table->dropForeign(['id_negocio_item']);
        });
    }
};
