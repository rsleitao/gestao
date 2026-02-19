<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Requerente extends Model
{
    use HasFactory;

    protected $table = 'requerentes';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'nif',
        'morada',
        'codigo_postal',
        'localidade',
        'notas',
    ];

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class);
    }
}
