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
    ];

    protected $casts = [
        'personal_info' => 'array',
        'addon_expo' => 'boolean',
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
}
