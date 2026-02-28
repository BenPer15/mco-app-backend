<?php

namespace App\Domains\Tracking\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class StepEntry extends BaseModel
{
    use HasUuids;

    protected $table = 'tracking_step_entries';

    protected $guarded = ['id'];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    /* Relationships */

    public function patientDay()
    {
        return $this->belongsTo(PatientDay::class, 'patient_day_id');
    }
}
