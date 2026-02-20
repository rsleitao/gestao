<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freguesias', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('id_concelho')->constrained('concelhos')->cascadeOnDelete();
            $table->timestamps();

            $table->index('id_concelho');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freguesias');
    }
};
