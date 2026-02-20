<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocio_itens', function (Blueprint $table) {
            $table->string('tipo_trabalho', 50)->nullable()->after('prazo_data'); // licenciamento ou execucao
            $table->index('tipo_trabalho');
        });
    }

    public function down(): void
    {
        Schema::table('negocio_itens', function (Blueprint $table) {
            $table->dropColumn('tipo_trabalho');
        });
    }
};
