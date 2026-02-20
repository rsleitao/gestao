<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropIndex(['estado', 'data_abertura']);
            $table->dropColumn(['estado', 'data_limite', 'data_conclusao']);
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->string('estado', 50)->default('aberto')->after('id_negocio_origem');
            $table->date('data_limite')->nullable()->after('data_abertura');
            $table->date('data_conclusao')->nullable()->after('data_limite');
            $table->index(['estado', 'data_abertura']);
        });
    }
};
