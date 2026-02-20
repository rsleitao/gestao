<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cc',
        'nif',
        'dgeg',
        'oet',
        'oe',
        'must_change_password',
        'password_changed_at',
        'id_role',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'ativo' => 'boolean',
            'password_changed_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /** Utilizador ativo (técnico). */
    public function isAtivo(): bool
    {
        return (bool) $this->ativo;
    }

    /** Deve alterar a password (ex.: primeiro login). */
    public function mustChangePassword(): bool
    {
        return (bool) $this->must_change_password;
    }

    /** Papel do utilizador (CEO, Responsável/Técnico por departamento). */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    /** CEO: id_role = 1. */
    public function isCEO(): bool
    {
        return (int) $this->id_role === Role::CEO;
    }

    /** Conta fixa CEO (não pode alterar papel nem desativar). Definida em config('app.fixed_ceo_user_id') ou config('app.fixed_ceo_email'). */
    public function isFixedCeo(): bool
    {
        if (config('app.fixed_ceo_user_id') && (int) config('app.fixed_ceo_user_id') === (int) $this->id) {
            return true;
        }
        if (config('app.fixed_ceo_email') && strcasecmp((string) config('app.fixed_ceo_email'), (string) $this->email) === 0) {
            return true;
        }

        return false;
    }

    /** Verifica se o utilizador tem a permissão (por papel). CEO tem todas; caso contrário consulta role->permissions. */
    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->isCEO()) {
            return true;
        }
        $role = $this->role;
        if (! $role) {
            return false;
        }

        return $role->permissions()->where('slug', $permissionSlug)->exists();
    }

    /** Pode gerir utilizadores (permissão manage-users). */
    public function isAdmin(): bool
    {
        return $this->hasPermission('manage-users');
    }
}
