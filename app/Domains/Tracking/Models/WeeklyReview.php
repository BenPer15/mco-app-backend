<?php

namespace App\Domains\Tracking\Models;

use App\Domains\Patient\Models\Patient;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReview extends BaseModel
{
    use HasUuids;

    protected $table = 'tracking_weekly_reviews';

    protected $guarded = ['id'];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];


    /* Relations */

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
