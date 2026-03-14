<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Registration extends Model
{
    use SoftDeletes, HasUuids;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAYMENT_PENDING = 'payment_pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'reference_code',
        'email',
        'personal_info',
        'current_step',
        'status',
        'total_amount',
        'referral_code',
        'agent_id',
        'addon_expo',
        'payment_method',
        'admin_review_status',
        'admin_review_notes',
        'admin_reviewed_at',
        'admin_reviewed_by',
    ];

    protected $casts = [
        'personal_info' => 'array',
        'addon_expo' => 'boolean',
        'admin_reviewed_at' => 'datetime',
    ];

    public function business(): HasOne
    {
        return $this->hasOne(Business::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function adminReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_reviewed_by');
    }
}
