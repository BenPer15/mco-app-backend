<?php

namespace App\Domains\Gamification\Models;

use App\Domains\Gamification\Enums\AchievementCategory;
use App\Domains\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends BaseModel
{
  use HasUuids;

  protected $table = 'gamification_achievements';

  protected $guarded = ['id'];

  protected $casts = [
    'category' => AchievementCategory::class,
    'points' => 'integer',
  ];

  public function patientAchievements(): HasMany
  {
    return $this->hasMany(PatientAchievement::class, 'achievement_id');
  }
}
