<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requerentes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('nif', 20)->nullable()->index();
            $table->string('morada')->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->string('localidade')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requerentes');
    }
};
