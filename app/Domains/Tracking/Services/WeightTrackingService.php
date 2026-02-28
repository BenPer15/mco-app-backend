<?php

namespace App\Domains\Tracking\Services;

use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Actions\Weight\CalculateBmiAction;
use App\Domains\Tracking\Actions\Weight\RecordWeightAction;
use App\Domains\Tracking\Data\WeightEntryData;
use App\Domains\Tracking\Models\WeightEntry;
use Illuminate\Support\Collection;

class WeightTrackingService
{
  public function __construct(
    private RecordWeightAction $recordWeight,
    private CalculateBmiAction $calculateBmi
  ) {}

  public function recordWeight(Patient $patient, array $data): WeightEntry
  {
    $dto = WeightEntryData::fromArray($data);
    return $this->recordWeight->execute($patient, $dto);
  }

  public function getWeightHistory(Patient $patient, int $days = 30): Collection
  {
    ray(WeightEntry::where('patient_id', $patient->id)
      ->orderBy('recorded_at', 'desc')
      ->get());
    return WeightEntry::where('patient_id', $patient->id)
      ->where('recorded_at', '>=', now()->subDays($days))
      ->orderBy('recorded_at', 'desc')
      ->get();
  }

  public function getWeightStats(Patient $patient): array
  {
    $allEntries = WeightEntry::where('patient_id', $patient->id)
      ->orderBy('recorded_at')
      ->get();

    if ($allEntries->isEmpty()) {
      return [
        'total_entries' => 0,
        'has_data' => false,
      ];
    }

    $firstEntry = $allEntries->first();
    $lastEntry = $allEntries->last();

    $totalWeightLost = $firstEntry->weight_kg - $lastEntry->weight_kg;
    $percentageLost = ($totalWeightLost / $firstEntry->weight_kg) * 100;

    // Calcul de la perte moyenne par semaine
    $daysBetween = $firstEntry->recorded_at->diffInDays($lastEntry->recorded_at);
    $weeksBetween = max($daysBetween / 7, 1);
    $averageWeeklyLoss = $totalWeightLost / $weeksBetween;

    return [
      'has_data' => true,
      'total_entries' => $allEntries->count(),
      'first_entry_date' => $firstEntry->recorded_at,
      'last_entry_date' => $lastEntry->recorded_at,
      'starting_weight' => $firstEntry->weight_kg,
      'current_weight' => $lastEntry->weight_kg,
      'total_weight_lost' => round($totalWeightLost, 2),
      'percentage_lost' => round($percentageLost, 2),
      'starting_bmi' => $firstEntry->bmi,
      'current_bmi' => $lastEntry->bmi,
      'bmi_improvement' => $firstEntry->bmi ? round($firstEntry->bmi - $lastEntry->bmi, 2) : null,
      'average_weekly_loss' => round($averageWeeklyLoss, 2),
      'bmi_category_start' => $firstEntry->bmi ? $this->calculateBmi->getCategory($firstEntry->bmi) : null,
      'bmi_category_current' => $lastEntry->bmi ? $this->calculateBmi->getCategory($lastEntry->bmi) : null,
      'trend' => $this->calculateTrend($allEntries),
    ];
  }

  public function getWeightChartData(Patient $patient, int $days = 30): array
  {
    $entries = collect($this->getWeightHistory($patient, $days));

    return [
      'labels' => $entries->pluck('recorded_at')->map(fn($date) => $date->format('d/m'))->toArray(),
      'weights' => $entries->pluck('weight_kg')->toArray(),
      'bmis' => $entries->pluck('bmi')->toArray(),
    ];
  }

  private function calculateTrend(Collection $entries): string
  {
    if ($entries->count() < 2) {
      return 'insufficient_data';
    }

    // Prendre les 5 dernières entrées pour la tendance récente
    $recentEntries = $entries->take(-5);

    $weights = $recentEntries->pluck('weight_kg')->toArray();
    $firstWeight = reset($weights);
    $lastWeight = end($weights);

    $difference = $firstWeight - $lastWeight;

    if (abs($difference) < 0.5) {
      return 'stable';
    }

    return $difference > 0 ? 'decreasing' : 'increasing';
  }

  public function getLatestWeight(Patient $patient): ?WeightEntry
  {
    return WeightEntry::where('patient_id', $patient->id)
      ->latest('recorded_at')
      ->first();
  }
}
