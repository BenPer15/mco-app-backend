<?php

namespace App\Domains\Tracking\Models;

use App\Domains\Patient\Models\Patient;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\Tracking\Enums\ActivityType;
use App\Domains\Tracking\Enums\IntensityLevel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityEntry extends BaseModel
{
    use HasUuids;

    protected $table = 'tracking_activity_entries';

    protected $guarded = ['id'];

    protected $casts = [
        'activity_type' => ActivityType::class,
        'intensity_level' => IntensityLevel::class,
        'duration_minutes' => 'integer',
        'borg_rating' => 'integer',
        'number_of_steps' => 'integer',
        'recorded_at' => 'datetime',
    ];

    /* Relationships */

    public function patientDay(): BelongsTo
    {
        return $this->belongsTo(PatientDay::class);
    }
}
