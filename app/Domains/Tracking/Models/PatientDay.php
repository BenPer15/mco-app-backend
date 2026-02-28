<?php

namespace App\Domains\Tracking\Models;

use App\Domains\Patient\Models\Patient;
use App\Domains\Shared\Models\BaseModel;
use Carbon\Carbon;
use DomainException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PatientDay extends BaseModel
{
    use HasUuids;

    protected $table = 'tracking_patient_days';

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
    ];

    /* Relationships */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityEntry::class);
    }

    public function nutritionEntries(): HasMany
    {
        return $this->hasMany(NutritionEntry::class);
    }

    public function stepEntry(): HasOne
    {
        return $this->hasOne(StepEntry::class);
    }
}
