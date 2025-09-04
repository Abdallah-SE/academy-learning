<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMembership extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'package_id',
        'start_date',
        'end_date',
        'status',
        'payment_method',
        'payment_status',
        'admin_verified',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'admin_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the membership
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the package for this membership
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(MembershipPackage::class);
    }

    /**
     * Check if membership is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    /**
     * Check if membership is expired
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if payment is completed
     */
    public function isPaymentCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }
}
