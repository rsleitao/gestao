<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    public const CEO = 1;
    public const RESPONSAVEL_GESTAO = 2;
    public const TECNICO_GESTAO = 3;
    public const RESPONSAVEL_PROJETOS = 4;
    public const TECNICO_PROJETOS = 5;
    public const RESPONSAVEL_EXPLORACAO = 6;
    public const TECNICO_EXPLORACAO = 7;

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_role');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function isCEO(): bool
    {
        return (int) $this->id === self::CEO;
    }

    public function isGestao(): bool
    {
        return in_array((int) $this->id, [self::RESPONSAVEL_GESTAO, self::TECNICO_GESTAO], true);
    }

    public function isProjetos(): bool
    {
        return in_array((int) $this->id, [self::RESPONSAVEL_PROJETOS, self::TECNICO_PROJETOS], true);
    }

    public function isExploracao(): bool
    {
        return in_array((int) $this->id, [self::RESPONSAVEL_EXPLORACAO, self::TECNICO_EXPLORACAO], true);
    }
}
