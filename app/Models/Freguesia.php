<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Freguesia extends Model
{
    use HasFactory;

    protected $table = 'freguesias';

    protected $fillable = [
        'nome',
        'id_concelho',
    ];

    public function concelho(): BelongsTo
    {
        return $this->belongsTo(Concelho::class, 'id_concelho');
    }

    public function imoveis(): HasMany
    {
        return $this->hasMany(Imovel::class, 'id_freguesia');
    }
}
