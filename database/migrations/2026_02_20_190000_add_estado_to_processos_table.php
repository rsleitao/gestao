<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estado do trabalho (processo): A fazer, Em execução, Pendente, Concluído.
     */
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->string('estado', 30)->default('a_fazer')->after('observacoes');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropColumn('estado');
        });
    }
};
