<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Distrito extends Model
{
    use HasFactory;

    protected $table = 'distritos';

    protected $fillable = [
        'nome',
    ];

    public function concelhos(): HasMany
    {
        return $this->hasMany(Concelho::class, 'id_distrito');
    }

    public function imoveis(): HasMany
    {
        return $this->hasMany(Imovel::class, 'id_distrito');
    }
}
