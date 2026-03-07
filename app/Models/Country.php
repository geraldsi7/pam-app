<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Country extends Model
{
    use SoftDeletes, HasUuids;
    
    protected $fillable = [
        'name',
        'code',
    ];

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
