<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'unidade',
        'preco_base',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'preco_base' => 'decimal:2',
        ];
    }

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class);
    }
}
