<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'referral_code',
        'commission_rate',
        'is_active',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
