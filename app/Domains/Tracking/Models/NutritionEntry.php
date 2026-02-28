<?php

namespace App\Domains\Tracking\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionEntry extends BaseModel
{
    use HasUuids;

    protected $table = 'tracking_nutrition_entries';

    protected $guarded = ['id'];

    protected $casts = [
        'proteins_ok' => 'boolean',
        'vegetables_ok' => 'boolean',
        'hydration_ok' => 'boolean',
        'texture_ok' => 'boolean',
        'recorded_at' => 'datetime',
    ];

    public function patientDay(): BelongsTo
    {
        return $this->belongsTo(PatientDay::class);
    }

    public function getCompliancePercentAttribute(): int
    {
        $total = collect([
            $this->proteins_ok,
            $this->vegetables_ok,
            $this->hydration_ok,
            $this->texture_ok,
        ])->filter()->count();

        return (int) round(($total / 4) * 100);
    }
}
