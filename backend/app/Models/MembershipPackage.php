<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MembershipPackage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'features',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
        ];
    }

    /**
     * Get all memberships for this package
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(UserMembership::class, 'package_id');
    }

    /**
     * Get active memberships for this package
     */
    public function activeMemberships(): HasMany
    {
        return $this->memberships()->where('status', 'active');
    }

    /**
     * Check if package is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get duration in months
     */
    public function getDurationInMonthsAttribute(): float
    {
        return round($this->duration_days / 30, 1);
    }
}
