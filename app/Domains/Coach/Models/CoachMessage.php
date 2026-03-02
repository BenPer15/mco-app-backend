<?php

namespace App\Domains\Coach\Models;

use App\Domains\Patient\Models\Patient;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachMessage extends BaseModel
{
    use HasUuids;

    protected $table = 'coach_messages';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'context_snapshot' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
