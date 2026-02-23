<?php

use App\Models\Negocio;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Negócios em "Em trabalho" com todos os trabalhos ainda em "A fazer"
     * devem voltar a "Aceite" (ninguém começou ainda).
     */
    public function up(): void
    {
        Negocio::where('status', 'em_trabalho')
            ->whereHas('trabalhos')
            ->whereDoesntHave('trabalhos', fn ($q) => $q->where('estado', '!=', 'a_fazer'))
            ->each(fn (Negocio $n) => $n->update(['status' => 'aceite']));
    }

    public function down(): void
    {
        //
    }
};
