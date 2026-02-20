<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos do antigo "tecnicos" que fazem sentido no users do Laravel.
     * TOTP/recovery: Laravel Fortify trata disso ao instalar; não criamos colunas aqui.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login', 100)->nullable()->unique()->after('email');
            $table->string('cc', 20)->nullable()->after('name');
            $table->string('nif', 20)->nullable()->after('cc');
            $table->string('dgeg', 50)->nullable()->after('nif');
            $table->string('oet', 50)->nullable()->after('dgeg');
            $table->string('oe', 50)->nullable()->after('oet');
            $table->boolean('must_change_password')->default(false)->after('password');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            $table->unsignedBigInteger('id_role')->nullable()->after('password_changed_at');
            $table->boolean('ativo')->default(true)->after('id_role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'login', 'cc', 'nif', 'dgeg', 'oet', 'oe',
                'must_change_password', 'password_changed_at', 'id_role', 'ativo'
            ]);
        });
    }
};
