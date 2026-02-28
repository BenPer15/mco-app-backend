<?php

namespace App\Domains\Gamification\Models;

use App\Domains\Patient\Models\Patient;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientAchievement extends BaseModel
{
    use HasUuids;

    protected $table = 'gamification_patient_achievements';

    protected $guarded = ['id'];

    protected $casts = [
        'unlocked_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
