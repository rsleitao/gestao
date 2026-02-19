<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Processo extends Model
{
    use HasFactory;

    protected $table = 'processos';

    protected $fillable = [
        'referencia',
        'requerente_id',
        'servico_id',
        'estado',
        'data_abertura',
        'data_limite',
        'data_conclusao',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_abertura' => 'date',
            'data_limite' => 'date',
            'data_conclusao' => 'date',
        ];
    }

    public const ESTADOS = ['aberto', 'em_analise', 'concluido', 'arquivado'];

    public function requerente(): BelongsTo
    {
        return $this->belongsTo(Requerente::class);
    }

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class);
    }
}
