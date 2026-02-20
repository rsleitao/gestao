<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Processo extends Model
{
    use HasFactory;

    protected $table = 'processos';

    protected $fillable = [
        'codigo',
        'requerente_id',
        'id_imovel',
        'id_negocio_origem',
        'designacao',
        'data_abertura',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_abertura' => 'date',
        ];
    }

    /**
     * Referência para organização (ano do trabalho + código fixo).
     * Ex.: 26-0014 = trabalhos de 2026 do processo com código 0014.
     * O ano reflete o ano atual (organização de pastas por ano).
     */
    public function getReferenciaAttribute(): string
    {
        return sprintf('%02d-%04d', (int) now()->format('y'), $this->codigo);
    }

    /**
     * Código formatado a 4 dígitos (ex.: 0014).
     */
    public function getCodigoFormatadoAttribute(): string
    {
        return sprintf('%04d', $this->codigo);
    }

    /**
     * Próximo código sequencial (único por processo, fixo para sempre).
     */
    public static function nextCodigo(): int
    {
        $max = (int) static::max('codigo');
        return $max + 1;
    }

    /**
     * Gera designação automática baseada na loja e concelho do imóvel.
     * Formato: "Nome da Loja - Nome do Concelho"
     */
    public static function gerarDesignacao(?int $imovelId): ?string
    {
        if (!$imovelId) {
            return null;
        }

        $imovel = Imovel::with(['loja', 'concelho'])->find($imovelId);
        
        if (!$imovel) {
            return null;
        }

        $partes = [];
        
        if ($imovel->loja) {
            $partes[] = $imovel->loja->nome;
        }
        
        if ($imovel->concelho) {
            $partes[] = $imovel->concelho->nome;
        }

        return !empty($partes) ? implode(' - ', $partes) : null;
    }

    public function requerente(): BelongsTo
    {
        return $this->belongsTo(Requerente::class);
    }

    public function imovel(): BelongsTo
    {
        return $this->belongsTo(Imovel::class, 'id_imovel');
    }

    public function negocios(): HasMany
    {
        return $this->hasMany(Negocio::class, 'id_processo');
    }

    public function negocioOrigem(): BelongsTo
    {
        return $this->belongsTo(Negocio::class, 'id_negocio_origem');
    }
}
