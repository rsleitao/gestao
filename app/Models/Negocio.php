<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Negocio extends Model
{
    use HasFactory;

    protected $table = 'negocios';

    protected $fillable = [
        'status',
        'id_requerente',
        'id_requerente_fatura',
        'id_imovel',
        'designacao',
        'observacoes',
        'id_processo',
        'id_tecnico',
        'data_convertido',
        'data_faturado',
    ];

    protected function casts(): array
    {
        return [
            'data_convertido' => 'datetime',
            'data_faturado' => 'datetime',
        ];
    }

    public const STATUS = [
        'pendente' => 'Pendente',
        'aceite' => 'Aceite',
        'cancelado' => 'Cancelado',
        'em_trabalho' => 'Em Trabalho',
        'concluido' => 'Concluído',
        'faturado' => 'Faturado',
    ];

    public function requerente(): BelongsTo
    {
        return $this->belongsTo(Requerente::class, 'id_requerente');
    }

    public function requerenteFatura(): BelongsTo
    {
        return $this->belongsTo(Requerente::class, 'id_requerente_fatura');
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class, 'id_imovel');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'id_processo');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id_tecnico');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(NegocioItem::class, 'id_negocio')->orderBy('ordem');
    }

    /**
     * Calcula o total do negócio (soma de todos os itens).
     */
    public function getTotalAttribute(): float
    {
        return (float) $this->itens->sum(fn($item) => $item->preco * $item->quantidade);
    }

    /**
     * Formata o total como string monetária.
     */
    public function getTotalFormatadoAttribute(): string
    {
        return number_format($this->total, 2, ',', ' ') . ' €';
    }

    /**
     * Verifica se o negócio está pendente de validação do cliente.
     */
    public function isPendente(): bool
    {
        return $this->status === 'pendente';
    }

    /**
     * Verifica se o negócio foi aceite pelo cliente.
     */
    public function isAceite(): bool
    {
        return $this->status === 'aceite';
    }

    /**
     * Verifica se o negócio foi cancelado.
     */
    public function isCancelado(): bool
    {
        return $this->status === 'cancelado';
    }

    /**
     * Verifica se já tem processo associado.
     */
    public function temProcesso(): bool
    {
        return !is_null($this->id_processo);
    }
}
