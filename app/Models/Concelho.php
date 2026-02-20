<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Concelho extends Model
{
    use HasFactory;

    protected $table = 'concelhos';

    protected $fillable = [
        'nome',
        'id_distrito',
    ];

    public function distrito(): BelongsTo
    {
        return $this->belongsTo(Distrito::class, 'id_distrito');
    }

    public function freguesias(): HasMany
    {
        return $this->hasMany(Freguesia::class, 'id_concelho');
    }

    public function imoveis(): HasMany
    {
        return $this->hasMany(Imovel::class, 'id_concelho');
    }
}
