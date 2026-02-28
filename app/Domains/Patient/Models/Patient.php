<?php

namespace App\Domains\Patient\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Domains\Core\Models\User;
use App\Domains\Patient\Enums\Gender;
use App\Domains\Patient\Enums\SurgeryType;
use App\Domains\Patient\Utils\BmiCalculator;
use App\Domains\Shared\Models\BaseModel;
use App\Domains\Tracking\Models\PatientDay;
use App\Domains\Tracking\Models\WeightEntry;
use App\Domains\Tracking\Models\WeeklyReview;
use Carbon\Carbon;
use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class Patient extends BaseModel
{
  use HasFactory, HasUuids;

  protected $table = 'patient_patients';

  protected $guarded = ['id'];

  protected $casts = [
    'birth_date' => 'datetime',
    'surgery_type' => SurgeryType::class,
    'gender' => Gender::class,
    'surgery_date' => 'datetime',
    'settings' => 'array',
  ];

  protected $appends = ['yo', 'current-weight', 'bmi'];

  protected static function newFactory(): Factory
  {
    return PatientFactory::new();
  }


  /* Relationships */

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function patientDays(): HasMany
  {
    return $this->hasMany(PatientDay::class);
  }

  public function weightEntries(): HasMany
  {
    return $this->hasMany(WeightEntry::class);
  }

  public function weeklyReviews(): HasMany
  {
    return $this->hasMany(WeeklyReview::class);
  }

  public function gamificationPoints(): HasOne
  {
    return $this->hasOne(\App\Domains\Gamification\Models\PatientPoint::class);
  }

  public function patientAchievements(): HasMany
  {
    return $this->hasMany(\App\Domains\Gamification\Models\PatientAchievement::class);
  }

  /* Protected */

  public function weights(): Collection
  {
    return $this->weightEntries;
  }

  /* Accessor */

  public function getYoAttribute(): int
  {
    $today = Date::now();
    $birth = $this->birth_date;

    return date_diff($today, $birth)->y;
  }

  public function getCurrentWeightAttribute(): float
  {
    return $this->weightEntries()->latest()->first()?->weight_kg ?? 0;
  }

  public function getBmiAttribute(): float
  {
    return $this->weightEntries()->latest()->first()?->bmi ?? 0;
  }
}
