<?php

namespace App\Domains\Tracking\Models;

use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class NutritionEntry extends BaseModel
{
    use HasUuids;

    protected $table = 'tracking_nutrition_entries';

    protected $guarded = ['id'];
}
