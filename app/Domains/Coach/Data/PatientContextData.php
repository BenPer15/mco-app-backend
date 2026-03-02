<?php

namespace App\Domains\Coach\Data;

final class PatientContextData
{
    public function __construct(
        public array $patient,
        public array $weightStats,
        public array $activityStats,
        public ?array $todayNutrition,
        public array $gamification,
        public ?array $latestWeeklyReview,
        public ?WeatherData $weather,
        public array $todayProgress = [],
        public array $nearAchievements = [],
    ) {}

    public function toPromptString(): string
    {
        $sections = [];

        // Patient profile
        $p = $this->patient;
        $sections[] = '## Profil patient';
        $sections[] = "- Prénom : {$p['first_name']}";
        $sections[] = "- Genre : {$p['gender']}";
        $sections[] = "- Âge : {$p['age']} ans";
        $sections[] = "- Taille : {$p['height_cm']} cm";
        $sections[] = "- Type d'opération : {$p['surgery_type']}";
        $sections[] = "- Date d'opération : {$p['surgery_date']}";
        $sections[] = "- Jours depuis l'opération : {$p['days_since_surgery']}";

        if (!empty($p['objectives'])) {
            $obj = $p['objectives'];
            if (isset($obj['steps'])) {
                $sections[] = "- Objectif pas quotidien : {$obj['steps']}";
            }
            if (isset($obj['activities'])) {
                $sections[] = "- Objectif activité quotidienne : {$obj['activities']} min";
            }
        }

        // Weight
        $sections[] = "\n## Suivi poids";
        if ($this->weightStats['has_data'] ?? false) {
            $w = $this->weightStats;
            $sections[] = "- Poids de départ : {$w['starting_weight']} kg";
            $sections[] = "- Poids actuel : {$w['current_weight']} kg";
            $sections[] = "- Perte totale : {$w['total_weight_lost']} kg ({$w['percentage_lost']}%)";
            $sections[] = "- IMC actuel : {$w['current_bmi']}";
            $sections[] = "- Tendance récente : {$w['trend']}";
            $sections[] = "- Perte moyenne/semaine : {$w['average_weekly_loss']} kg";
        } else {
            $sections[] = '- Aucune donnée de poids enregistrée';
        }

        // Activity
        $sections[] = "\n## Activité physique (30 derniers jours)";
        if ($this->activityStats['has_data'] ?? false) {
            $a = $this->activityStats;
            $sections[] = "- Nombre d'activités : {$a['total_activities']}";
            $sections[] = "- Durée totale : {$a['total_hours']} heures";
            $sections[] = "- Activités par semaine : {$a['activities_per_week']}";
            $sections[] = '- Activité préférée : ' . ($a['most_common_type'] ?? 'non définie');
            $sections[] = "- Pas total : {$a['total_steps']}";
        } else {
            $sections[] = '- Aucune activité enregistrée';
        }

        // Nutrition today
        $sections[] = "\n## Nutrition aujourd'hui";
        if ($this->todayNutrition) {
            $n = $this->todayNutrition;
            $sections[] = '- Protéines : ' . ($n['proteins_ok'] ? 'OK' : 'Non fait');
            $sections[] = '- Légumes : ' . ($n['vegetables_ok'] ? 'OK' : 'Non fait');
            $sections[] = '- Hydratation : ' . ($n['hydration_ok'] ? 'OK' : 'Non fait');
            $sections[] = '- Texture : ' . ($n['texture_ok'] ? 'OK' : 'Non fait');
        } else {
            $sections[] = "- Suivi nutritionnel non encore rempli aujourd'hui";
        }

        // Today's progress
        if (! empty($this->todayProgress)) {
            $sections[] = "\n## Progression du jour";

            $steps = $this->todayProgress['steps'] ?? [];
            if (! empty($steps['goal'])) {
                $stepsCurrent = $steps['current'] ?? 0;
                $stepsGoal = $steps['goal'];
                $stepsPercent = $steps['percent'] ?? 0;
                $sections[] = "- Pas aujourd'hui : {$stepsCurrent}/{$stepsGoal} ({$stepsPercent}%)";
            } elseif (($steps['current'] ?? 0) > 0) {
                $sections[] = "- Pas aujourd'hui : {$steps['current']} (pas d'objectif défini)";
            } else {
                $sections[] = '- Pas aujourd\'hui : Aucun pas enregistré';
            }

            $act = $this->todayProgress['activities'] ?? [];
            $actCount = $act['count'] ?? 0;
            $actMinutes = $act['total_minutes'] ?? 0;
            if ($actCount > 0) {
                $actLine = "- Activités aujourd'hui : {$actCount} activité(s), {$actMinutes} min";
                if (! empty($act['goal_minutes'])) {
                    $actLine .= "/{$act['goal_minutes']} min ({$act['percent']}%)";
                }
                if (! empty($act['types'])) {
                    $actLine .= ' (' . implode(', ', $act['types']) . ')';
                }
                $sections[] = $actLine;
            } else {
                $sections[] = '- Activités aujourd\'hui : Aucune activité enregistrée';
            }

            $nutr = $this->todayProgress['nutrition'] ?? [];
            if ($nutr['filled'] ?? false) {
                $sections[] = "- Nutrition aujourd'hui : {$nutr['pillars_completed']}/4 piliers complétés";
            } else {
                $sections[] = '- Nutrition aujourd\'hui : Suivi non encore rempli';
            }
        }

        // Gamification
        $sections[] = "\n## Gamification";
        $g = $this->gamification;
        $sections[] = "- Points totaux : {$g['total_points']}";
        $sections[] = "- Série en cours : {$g['current_streak_days']} jours";
        $sections[] = "- Plus longue série : {$g['longest_streak_days']} jours";
        $sections[] = "- Succès débloqués : {$g['unlocked_achievements']}/{$g['total_achievements']}";

        // Near achievements
        if (! empty($this->nearAchievements)) {
            $sections[] = "\n## Succès proches à débloquer";
            foreach ($this->nearAchievements as $na) {
                $remaining = $na['remaining'];
                $unit = $na['unit'];
                $progress = $na['progress_percent'] ?? '~';
                $sections[] = "- \"{$na['name']}\" : encore {$remaining} {$unit} — progression {$progress}%";
            }
        }

        // Weekly review
        if ($this->latestWeeklyReview) {
            $sections[] = "\n## Dernière revue hebdomadaire";
            $r = $this->latestWeeklyReview;
            $sections[] = "- Score physique : {$r['physical_score']}/10";
            $sections[] = "- Score mental : {$r['mental_score']}/10";
            $sections[] = "- Score adhérence : {$r['adherence_score']}/10";
            if (!empty($r['comment'])) {
                $sections[] = "- Commentaire : \"{$r['comment']}\"";
            }
        }

        // Weather
        if ($this->weather) {
            $sections[] = "\n## Météo";
            $sections[] = $this->weather->toPromptString();
        }

        return implode("\n", $sections);
    }

    public function toArray(): array
    {
        return [
            'patient' => $this->patient,
            'weight_stats' => $this->weightStats,
            'activity_stats' => $this->activityStats,
            'today_nutrition' => $this->todayNutrition,
            'today_progress' => $this->todayProgress,
            'gamification' => $this->gamification,
            'near_achievements' => $this->nearAchievements,
            'latest_weekly_review' => $this->latestWeeklyReview,
            'weather' => $this->weather?->toArray(),
        ];
    }
}
