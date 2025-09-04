<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
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
        'phone',
        'whatsapp_verified',
        'role',
        'status',
        'avatar',
        'date_of_birth',
        'gender',
        'country',
        'timezone',
        'preferences',
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
            'whatsapp_verified' => 'boolean',
            'date_of_birth' => 'date',
            'preferences' => 'array',
        ];
    }

    /**
     * Get the user's active membership
     */
    public function activeMembership(): HasOne
    {
        return $this->hasOne(UserMembership::class)->where('status', 'active');
    }

    /**
     * Get all user memberships
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class);
    }

    /**
     * Check if user has active membership
     */
    public function hasActiveMembership(): bool
    {
        return $this->activeMembership()->exists();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is teacher
     */
    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Get the highest role level for the user
     */
    public function getHighestRoleLevel(): int
    {
        $highestLevel = 0;
        
        foreach ($this->roles as $role) {
            if ($role->level > $highestLevel) {
                $highestLevel = $role->level;
            }
        }
        
        return $highestLevel;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission($permissions): bool
    {
        if (is_string($permissions)) {
            return $this->hasPermissionTo($permissions);
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles): bool
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}
