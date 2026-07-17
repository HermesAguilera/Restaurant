<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cualquier usuario con al menos un rol puede acceder al panel.
     * El bypass de permisos para 'root' se maneja en Gate::before (AppServiceProvider).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Siempre permitir el acceso al usuario principal o a quienes tengan el rol 'root'
        if ($this->email === 'admin@admin.com' || $this->email === 'root@example.com' || $this->hasRole('root')) {
            return true;
        }

        // Si no es root, debe tener al menos un rol asignado para poder entrar al panel
        return $this->roles()->exists();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return null;
    }
}
