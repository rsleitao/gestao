<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Imovel extends Model
{
    use HasFactory;

    protected $table = 'imoveis';

    protected $fillable = [
        'nip',
        'morada',
        'id_distrito',
        'id_concelho',
        'id_freguesia',
        'cod_postal',
        'localidade_imovel',
        'coordenadas',
        'potencia',
        'tensao',
        'area_imovel',
        'pisos',
        'tipo_imovel',
        'id_loja',
        'pts',
        'ggs',
        'pcves',
        'descricao',
    ];

    protected function casts(): array
    {
        return [
            'potencia' => 'decimal:2',
            'area_imovel' => 'decimal:2',
            'pts' => 'array',   // ex: ["600kVA", "600kVA"]
            'ggs' => 'array',   // ex: ["250kVA"]
            'pcves' => 'array', // ex: ["50kVA", "50kVA"]
        ];
    }

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class, 'id_concelho');
    }

    public function freguesia(): BelongsTo
    {
        return $this->belongsTo(Freguesia::class, 'id_freguesia');
    }

    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class, 'id_loja');
    }

    public function negocios(): HasMany
    {
        return $this->hasMany(Negocio::class, 'id_imovel');
    }

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class, 'id_imovel');
    }
}
