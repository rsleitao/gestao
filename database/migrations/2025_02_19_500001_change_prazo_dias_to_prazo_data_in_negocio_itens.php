<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocio_itens', function (Blueprint $table) {
            $table->dropColumn('prazo_dias');
            $table->date('prazo_data')->nullable()->after('quantidade');
        });
    }

    public function down(): void
    {
        Schema::table('negocio_itens', function (Blueprint $table) {
            $table->dropColumn('prazo_data');
            $table->integer('prazo_dias')->nullable()->after('quantidade');
        });
    }
};
