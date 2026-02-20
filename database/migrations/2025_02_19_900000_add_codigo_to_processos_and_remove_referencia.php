<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->unsignedInteger('codigo')->nullable()->after('id');
        });

        $rows = DB::table('processos')->orderBy('id')->get(['id', 'referencia']);
        $maxCodigo = 0;
        foreach ($rows as $row) {
            $codigo = null;
            if (preg_match('/^\d{2}-(\d+)$/', $row->referencia, $m)) {
                $codigo = (int) $m[1];
            }
            if ($codigo === null) {
                $maxCodigo++;
                $codigo = $maxCodigo;
            } else {
                $maxCodigo = max($maxCodigo, $codigo);
            }
            DB::table('processos')->where('id', $row->id)->update(['codigo' => $codigo]);
        }

        Schema::table('processos', function (Blueprint $table) {
            $table->unique('codigo');
        });

        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn('referencia');
        });
    }

    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->string('referencia', 50)->nullable()->after('id');
        });

        $year = (int) now()->format('y');
        DB::table('processos')->orderBy('id')->get(['id', 'codigo'])->each(function ($row) use ($year) {
            DB::table('processos')->where('id', $row->id)->update([
                'referencia' => sprintf('%02d-%04d', $year, $row->codigo),
            ]);
        });

        Schema::table('processos', function (Blueprint $table) {
            $table->string('referencia', 50)->nullable(false)->unique()->change();
            $table->dropUnique(['codigo']);
            $table->dropColumn('codigo');
        });
    }
};
