<?php

namespace App\Domains\Tracking\Models;

use App\Domains\Patient\Models\Patient;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeightEntry extends BaseModel
{
    use HasUuids;

    protected $table = 'tracking_weight_entries';

    protected $guarded = ['id'];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'bmi' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    /* Relationships */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
