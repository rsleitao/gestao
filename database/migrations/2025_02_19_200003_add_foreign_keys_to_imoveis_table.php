<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('imoveis', function (Blueprint $table) {
            $table->foreign('id_distrito')->references('id')->on('distritos')->nullOnDelete();
            $table->foreign('id_concelho')->references('id')->on('concelhos')->nullOnDelete();
            $table->foreign('id_freguesia')->references('id')->on('freguesias')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('imoveis', function (Blueprint $table) {
            $table->dropForeign(['id_distrito']);
            $table->dropForeign(['id_concelho']);
            $table->dropForeign(['id_freguesia']);
        });
    }
};
