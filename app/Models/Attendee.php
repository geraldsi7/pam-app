<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Attendee extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'business_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'additional_details',
    ];

    protected $casts = [
        'additional_details' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
