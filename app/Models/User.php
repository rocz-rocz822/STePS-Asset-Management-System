<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'google_id',
        'password',
        'role',
        'is_active',
        'can_manage_assets',
        'is_protected',
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
            'is_active' => 'boolean',
            'can_manage_assets' => 'boolean',
            'is_protected' => 'boolean',
        ];
    }

    /**
     * Role helpers — used throughout controllers, policies, and Blade views.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function canManageAssets(): bool
    {
        return $this->isAdmin() || $this->can_manage_assets;
    }

    /**
     * Get users that this user is allowed to assign assets to.
     */
    public function assignableUsers(): \Illuminate\Database\Eloquent\Builder
    {
        $query = static::query()
            ->where('is_active', true)
            ->orderBy('name');

        // Admin can assign assets to anyone.
        if ($this->isAdmin()) {
            return $query;
        }

        // Technicians can assign assets to:
        // - themselves
        // - other technicians
        // - staff
        if ($this->isTechnician()) {
            return $query->whereIn('role', ['technician', 'staff']);
        }

        // Staff can only assign assets to themselves.
        return $query->where('id', $this->id);
    }

    /**
     * Account deletion requests submitted by this user.
     */
    public function accountDeletionRequests(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AccountDeletionRequest::class);
    }
}