<?php

namespace Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'avatar',
        'status',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'email_verified_at',
        'password_changed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'two_factor_recovery_codes' => 'array',
        'two_factor_enabled' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'avatar_url',
        'is_super_admin',
        'is_moderator',
    ];

    /**
     * Get the admin's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Check if admin is super admin.
     */
    public function getIsSuperAdminAttribute(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if admin is moderator.
     */
    public function getIsModeratorAttribute(): bool
    {
        return $this->hasRole('moderator');
    }

    /**
     * Check if admin is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if admin is suspended.
     */
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if admin can access admin panel.
     */
    public function canAccessAdminPanel(): bool
    {
        return $this->isActive() && $this->hasAnyRole(['admin', 'super_admin', 'moderator']);
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(string $ip, string $userAgent): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
            'last_login_user_agent' => $userAgent,
        ]);
    }

    /**
     * Scope for active admins.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for admins by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Scope for super admins.
     */
    public function scopeSuperAdmins($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'super_admin');
        });
    }

    /**
     * Scope for moderators.
     */
    public function scopeModerators($query)
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'moderator');
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return new \Modules\Admin\Database\Factories\AdminFactory();
    }

    /**
     * Get the default guard name for the model.
     */
    public function getDefaultGuardName(): string
    {
        return 'admin';
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
