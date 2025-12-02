<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ⭐ Filament 4 – pozwala użytkownikowi wejść do panelu
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // później zrobimy ->is_admin
    }

    /**
     * ⭐ Filament 4 – bez tego login potrafi się wysypać
     */
    public function getFilamentName(): ?string
    {
        return $this->name;
    }
}
