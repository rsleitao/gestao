<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Papéis por departamento: CEO + Gestão (Resp/Téc) + Projetos (Resp/Téc) + Exploração (Resp/Téc).
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // ex: "CEO", "Responsável Gestão"
            $table->string('slug', 50)->unique(); // ex: "ceo", "responsavel-gestao"
        });

        $roles = [
            ['id' => 1, 'name' => 'CEO', 'slug' => 'ceo'],
            ['id' => 2, 'name' => 'Responsável Gestão', 'slug' => 'responsavel-gestao'],
            ['id' => 3, 'name' => 'Técnico Gestão', 'slug' => 'tecnico-gestao'],
            ['id' => 4, 'name' => 'Responsável Projetos', 'slug' => 'responsavel-projetos'],
            ['id' => 5, 'name' => 'Técnico Projetos', 'slug' => 'tecnico-projetos'],
            ['id' => 6, 'name' => 'Responsável Exploração', 'slug' => 'responsavel-exploracao'],
            ['id' => 7, 'name' => 'Técnico Exploração', 'slug' => 'tecnico-exploracao'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('id_role')->references('id')->on('roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_role']);
        });
        Schema::dropIfExists('roles');
    }
};
