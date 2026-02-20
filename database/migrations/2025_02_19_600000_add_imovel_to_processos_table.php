<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->foreignId('id_imovel')->nullable()->after('requerente_id')->constrained('imoveis')->nullOnDelete();
            $table->index('id_imovel');
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropForeign(['id_imovel']);
            $table->dropColumn('id_imovel');
        });
    }
};
