<?php

namespace App\Domains\Gamification\Models;

use App\Domains\Patient\Models\Patient;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientPoint extends BaseModel
{
    use HasUuids;

    protected $table = 'gamification_patient_points';

    protected $guarded = ['id'];

    protected $casts = [
        'total_points' => 'integer',
        'current_streak_days' => 'integer',
        'longest_streak_days' => 'integer',
        'last_activity_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
