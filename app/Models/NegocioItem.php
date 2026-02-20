<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegocioItem extends Model
{
    use HasFactory;

    protected $table = 'negocio_itens';

    protected $fillable = [
        'id_negocio',
        'descricao',
        'preco',
        'quantidade',
        'prazo_data',
        'tipo_trabalho',
        'ordem',
    ];

    protected function casts(): array
    {
        return [
            'preco' => 'decimal:2',
            'quantidade' => 'decimal:2',
            'prazo_data' => 'date',
            'ordem' => 'integer',
        ];
    }

    public const TIPOS_TRABALHO = [
        'licenciamento' => 'Licenciamento',
        'execucao' => 'Execução',
    ];

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio');
    }

    /**
     * Calcula o total deste item (preço × quantidade).
     */
    public function getTotalAttribute(): float
    {
        return (float) ($this->preco * $this->quantidade);
    }

    /**
     * Formata o total como string monetária.
     */
    public function getTotalFormatadoAttribute(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }
}
