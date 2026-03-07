<?php

namespace App\Models;

use App\Models\MatchRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Business extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'registration_id',
        'company_name',
        'origin',
        'ticket_type',
        'business_details',
        'country_id',
        'email',
        'phone',
        'attendance_mode',
        'is_public',
        'matchmaking_interests',
    ];

    protected $casts = [
        'business_details' => 'array',
        'matchmaking_interests' => 'array',
        'is_public' => 'boolean',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public function industries(): BelongsToMany
    {
        return $this->belongsToMany(Industry::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function sentMatchRequests(): HasMany
    {
        return $this->hasMany(MatchRequest::class, 'requester_business_id');
    }

    public function receivedMatchRequests(): HasMany
    {
        return $this->hasMany(MatchRequest::class, 'target_business_id');
    }
}
