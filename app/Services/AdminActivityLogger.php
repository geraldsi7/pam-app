<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLogger
{
    public function log(
        string $action,
        ?Model $subject = null,
        int|string|null $actorId = null,
        string $description = '',
        array $metadata = []
    ): void {
        AdminActivityLog::create([
            'actor_id' => $actorId,
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
