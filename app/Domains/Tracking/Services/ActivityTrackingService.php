<?php

namespace App\Domains\Tracking\Services;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Actions\Activity\RecordActivityAction;
use App\Domains\Tracking\Data\ActivityEntryData;
use App\Domains\Tracking\Models\ActivityEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActivityTrackingService
{
  public function __construct(
    private RecordActivityAction $recordActivity
  ) {}

  public function recordActivity(Patient $patient, array $data): ActivityEntry
  {
    $dto = ActivityEntryData::fromArray($data);

    return $this->recordActivity->execute($patient, $dto);
  }

  public function getActivityHistory(Patient $patient, int $days = 30): Collection
  {
    return ActivityEntry::whereHas('patientDay', function ($query) use ($patient) {
      $query->where('patient_id', $patient->id);
    })
      ->where('recorded_at', '>=', now()->subDays($days))
      ->with('patientDay')
      ->orderBy('recorded_at', 'desc')
      ->get();
  }

  public function getActivityStats(Patient $patient, int $days = 30): array
  {
    $activities = $this->getActivityHistory($patient, $days);

    if ($activities->isEmpty()) {
      return [
        'total_activities' => 0,
        'has_data' => false,
      ];
    }

    $totalMinutes = $activities->sum('duration_minutes');
    $totalHours = round($totalMinutes / 60, 1);

    $activitiesByType = $activities->groupBy('activity_type')->map(function ($group) {
      return [
        'count' => $group->count(),
        'total_minutes' => $group->sum('duration_minutes'),
      ];
    });

    $activitiesByIntensity = $activities->groupBy('intensity_level')->map(function ($group) {
      return [
        'count' => $group->count(),
        'total_minutes' => $group->sum('duration_minutes'),
      ];
    });

    return [
      'has_data' => true,
      'total_activities' => $activities->count(),
      'total_minutes' => $totalMinutes,
      'total_hours' => $totalHours,
      'average_duration' => round($activities->avg('duration_minutes'), 1),
      'activities_per_week' => round(($activities->count() / $days) * 7, 1),
      'by_type' => $activitiesByType,
      'by_intensity' => $activitiesByIntensity,
      'most_common_type' => $activitiesByType->sortByDesc('count')->keys()->first(),
      'total_steps' => $activities->whereNotNull('number_of_steps')->sum('number_of_steps'),
    ];
  }

  public function getCurrentWeekActivities(Patient $patient): Collection
  {
    return ActivityEntry::whereHas('patientDay', function ($query) use ($patient) {
      $query->where('patient_id', $patient->id);
    })
      ->whereBetween('recorded_at', [
        now()->startOfWeek(),
        now()->endOfWeek()
      ])
      ->with('patientDay')
      ->orderBy('recorded_at', 'desc')
      ->get();
  }

  public function getDayActivities(Patient $patient, ?string $date = null): Collection
  {
    $targetDate = $date ? Carbon::parse($date)->toDateString() : today()->toDateString();

    return ActivityEntry::whereHas('patientDay', function ($query) use ($patient, $targetDate) {
      $query->where('patient_id', $patient->id)
        ->where('date', $targetDate);
    })
      ->with('patientDay')
      ->orderBy('recorded_at', 'desc')
      ->get();
  }
}
