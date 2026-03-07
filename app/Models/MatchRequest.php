<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MatchRequest extends Model
{
    use HasUuids;

    protected $fillable = [
        'requester_business_id',
        'target_business_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function requesterBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'requester_business_id');
    }

    public function targetBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'target_business_id');
    }
}
