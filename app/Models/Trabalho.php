<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trabalho extends Model
{
    protected $table = 'trabalhos';

    protected $fillable = [
        'id_negocio',
        'id_negocio_item',
        'id_tecnico',
        'id_servico',
        'tipo_trabalho',
        'designacao',
        'estado',
        'ordem',
        'prazo',
    ];

    public const TIPOS_TRABALHO = [
        'licenciamento' => 'Licenciamento',
        'execucao' => 'Execução',
    ];

    protected function casts(): array
    {
        return [
            'prazo' => 'date',
        ];
    }

    public const ESTADO_A_FAZER = 'a_fazer';
    public const ESTADO_EM_EXECUCAO = 'em_execucao';
    public const ESTADO_PENDENTE = 'pendente';
    public const ESTADO_CONCLUIDO = 'concluido';

    public const ESTADOS = [
        self::ESTADO_A_FAZER => 'A fazer',
        self::ESTADO_EM_EXECUCAO => 'Em execução',
        self::ESTADO_PENDENTE => 'Pendente',
        self::ESTADO_CONCLUIDO => 'Concluído',
    ];

    public function negocio(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio');
    }

    public function negocioItem(): BelongsTo
    {
        return $this->belongsTo(NegocioItem::class, 'id_negocio_item');
    }

    /** Designação do negócio (para o card). */
    public function getDesignacaoNegocioAttribute(): ?string
    {
        return $this->relationLoaded('negocio') && $this->negocio ? $this->negocio->designacao : null;
    }

    /** Item do negócio para exibição: o associado ao trabalho ou o primeiro do negócio (fallback para dados antigos). */
    protected function getItemParaExibicao(): ?NegocioItem
    {
        if ($this->relationLoaded('negocioItem') && $this->negocioItem) {
            return $this->negocioItem;
        }
        if ($this->relationLoaded('negocio') && $this->negocio && $this->negocio->relationLoaded('itens') && $this->negocio->itens->isNotEmpty()) {
            return $this->negocio->itens->first();
        }
        return null;
    }

    /** Descrição do serviço: do item do negócio (negocio_itens.descricao). */
    public function getDescricaoServicoAttribute(): ?string
    {
        $item = $this->getItemParaExibicao();
        return $item ? $item->descricao : null;
    }

    /** Tipo (licenciamento/execução): do item (negocio_itens.tipo_trabalho). */
    public function getTipoTrabalhoParaExibicaoAttribute(): ?string
    {
        $item = $this->getItemParaExibicao();
        if (!$item || !$item->tipo_trabalho) {
            return null;
        }
        return self::TIPOS_TRABALHO[$item->tipo_trabalho] ?? $item->tipo_trabalho;
    }

    /** Prazo: do item (negocio_itens.prazo_data). */
    public function getPrazoParaExibicaoAttribute(): ?\Carbon\Carbon
    {
        $item = $this->getItemParaExibicao();
        return $item ? $item->prazo_data : null;
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_tecnico');
    }

    public function servico(): BelongsTo
    {
        return $this->belongsTo(Servico::class, 'id_servico');
    }

    public function isConcluido(): bool
    {
        return $this->estado === self::ESTADO_CONCLUIDO;
    }
}
