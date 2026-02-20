<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocios', function (Blueprint $table) {
            // A quem faturar (pode ser diferente do requerente)
            $table->foreignId('id_requerente_fatura')->nullable()->after('id_requerente')->constrained('requerentes')->nullOnDelete();
            $table->index('id_requerente_fatura');
        });
    }

    public function down(): void
    {
        Schema::table('negocios', function (Blueprint $table) {
            $table->dropForeign(['id_requerente_fatura']);
            $table->dropColumn('id_requerente_fatura');
        });
    }
};
