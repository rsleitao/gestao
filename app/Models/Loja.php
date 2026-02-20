<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Loja extends Model
{
    use HasFactory;

    protected $table = 'lojas';

    protected $fillable = [
        'nome',
        'ativo',
        'imagem',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function imoveis(): HasMany
    {
        return $this->hasMany(Imovel::class, 'id_loja');
    }

    /**
     * URL pública da imagem da loja (para usar no site).
     * Retorna null se não houver imagem.
     */
    public function getImagemUrlAttribute(): ?string
    {
        if (empty($this->imagem)) {
            return null;
        }

        return Storage::url($this->imagem);
    }
}
