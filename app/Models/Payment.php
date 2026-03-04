<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Payment extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'registration_id',
        'method',
        'status',
        'amount',
        'transaction_reference',
        'payment_details',
    ];

    protected $casts = [
        'payment_details' => 'array',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
