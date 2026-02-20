<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permissões geridas pelo CEO no ecrã (sem alterar código).
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // ex: "Acesso Gestão"
            $table->string('slug', 50)->unique(); // ex: "access-gestao" (usado nos Gates)
        });

        $permissions = [
            ['id' => 1, 'name' => 'Acesso Gestão (Serviços, Negócios)', 'slug' => 'access-gestao'],
            ['id' => 2, 'name' => 'Acesso Projetos (Processos, Requerentes)', 'slug' => 'access-projetos'],
            ['id' => 3, 'name' => 'Acesso Exploração (Lojas, Imóveis)', 'slug' => 'access-exploracao'],
            ['id' => 4, 'name' => 'Área Administrador (Freguesias, Permissões)', 'slug' => 'access-admin'],
            ['id' => 5, 'name' => 'Gerir utilizadores', 'slug' => 'manage-users'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->insert($p);
        }

        Schema::create('role_permission', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->primary(['role_id', 'permission_id']);
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
            $table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
        });

        // CEO (role_id 1) = todas as permissões; Gestão 2,3 = access-gestao; Projetos 4,5 = access-projetos; Exploração 6,7 = access-exploracao
        $rolePermission = [
            [1, 1], [1, 2], [1, 3], [1, 4], [1, 5], // CEO
            [2, 1], [3, 1],                           // Resp. e Téc. Gestão
            [4, 2], [5, 2],                          // Resp. e Téc. Projetos
            [6, 3], [7, 3],                          // Resp. e Téc. Exploração
        ];

        foreach ($rolePermission as [$roleId, $permId]) {
            DB::table('role_permission')->insert(['role_id' => $roleId, 'permission_id' => $permId]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
    }
};
