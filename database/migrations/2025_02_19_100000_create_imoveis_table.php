<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 25)->nullable();
            $table->string('morada');
            $table->unsignedBigInteger('id_distrito')->nullable(); // FK para distritos (tabela a criar)
            $table->unsignedBigInteger('id_concelho')->nullable(); // FK para concelhos (tabela a criar)
            $table->unsignedBigInteger('id_freguesia')->nullable(); // FK para freguesias (tabela a criar)
            $table->string('cod_postal');
            $table->string('localidade_imovel');
            $table->string('coordenadas', 255)->nullable();
            $table->decimal('potencia', 10, 2)->nullable();
            $table->string('tensao', 20)->nullable();
            $table->decimal('area_imovel', 10, 2)->nullable();
            $table->integer('pisos')->nullable();
            $table->string('tipo_imovel', 50)->nullable();
            $table->unsignedBigInteger('id_loja')->nullable(); // FK para lojas (tabela a criar)
            // Um ou mais valores (ex: 600kVA). Guardados como JSON, ex: ["600kVA", "600kVA"]
            $table->json('pts')->nullable();
            $table->json('ggs')->nullable();
            $table->json('pcves')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();

            $table->index('id_distrito');
            $table->index('id_concelho');
            $table->index('id_freguesia');
            $table->index('id_loja');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};
