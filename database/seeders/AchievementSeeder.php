<?php

namespace Database\Seeders;

use App\Domains\Gamification\Enums\AchievementCategory;
use App\Domains\Gamification\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            // --- POIDS ---
            ['code' => 'first_weight', 'name' => 'Premier pas sur la balance', 'description' => 'Enregistrez votre premier poids.', 'icon' => 'lucide:scale', 'points' => 10, 'category' => AchievementCategory::WEIGHT],
            ['code' => 'weight_5kg_lost', 'name' => '5 kg perdus', 'description' => 'Vous avez perdu 5 kg depuis le début.', 'icon' => 'lucide:trending-down', 'points' => 50, 'category' => AchievementCategory::WEIGHT],
            ['code' => 'weight_10kg_lost', 'name' => '10 kg perdus', 'description' => 'Vous avez perdu 10 kg depuis le début.', 'icon' => 'lucide:trending-down', 'points' => 100, 'category' => AchievementCategory::WEIGHT],
            ['code' => 'weight_20kg_lost', 'name' => '20 kg perdus', 'description' => 'Vous avez perdu 20 kg depuis le début.', 'icon' => 'lucide:trophy', 'points' => 200, 'category' => AchievementCategory::WEIGHT],
            ['code' => 'weight_30kg_lost', 'name' => '30 kg perdus', 'description' => 'Vous avez perdu 30 kg. Incroyable !', 'icon' => 'lucide:trophy', 'points' => 300, 'category' => AchievementCategory::WEIGHT],
            ['code' => 'weight_50kg_lost', 'name' => '50 kg perdus', 'description' => 'Vous avez perdu 50 kg. Extraordinaire !', 'icon' => 'lucide:crown', 'points' => 500, 'category' => AchievementCategory::WEIGHT],
            ['code' => 'weight_10_entries', 'name' => 'Suivi régulier', 'description' => '10 pesées enregistrées.', 'icon' => 'lucide:clipboard-list', 'points' => 30, 'category' => AchievementCategory::WEIGHT],

            // --- ACTIVITÉ ---
            ['code' => 'first_activity', 'name' => 'Première activité', 'description' => 'Enregistrez votre première activité physique.', 'icon' => 'lucide:dumbbell', 'points' => 10, 'category' => AchievementCategory::ACTIVITY],
            ['code' => 'activity_10', 'name' => '10 activités', 'description' => '10 activités physiques enregistrées.', 'icon' => 'lucide:dumbbell', 'points' => 50, 'category' => AchievementCategory::ACTIVITY],
            ['code' => 'activity_25', 'name' => '25 activités', 'description' => '25 activités physiques enregistrées.', 'icon' => 'lucide:dumbbell', 'points' => 100, 'category' => AchievementCategory::ACTIVITY],
            ['code' => 'activity_50', 'name' => '50 activités', 'description' => '50 activités physiques enregistrées.', 'icon' => 'lucide:medal', 'points' => 150, 'category' => AchievementCategory::ACTIVITY],
            ['code' => 'activity_100', 'name' => '100 activités', 'description' => '100 activités. Vous êtes un(e) champion(ne) !', 'icon' => 'lucide:trophy', 'points' => 300, 'category' => AchievementCategory::ACTIVITY],
            ['code' => 'activity_60min', 'name' => 'Session longue', 'description' => 'Complétez une session de 60 minutes ou plus.', 'icon' => 'lucide:timer', 'points' => 25, 'category' => AchievementCategory::ACTIVITY],

            // --- SÉRIE ---
            ['code' => 'streak_3', 'name' => 'Série de 3 jours', 'description' => 'Actif 3 jours consécutifs.', 'icon' => 'lucide:flame', 'points' => 15, 'category' => AchievementCategory::STREAK],
            ['code' => 'streak_7', 'name' => 'Série de 7 jours', 'description' => 'Actif 7 jours consécutifs. Une semaine complète !', 'icon' => 'lucide:flame', 'points' => 50, 'category' => AchievementCategory::STREAK],
            ['code' => 'streak_14', 'name' => 'Série de 14 jours', 'description' => 'Actif 14 jours consécutifs. Deux semaines !', 'icon' => 'lucide:flame', 'points' => 100, 'category' => AchievementCategory::STREAK],
            ['code' => 'streak_30', 'name' => 'Série de 30 jours', 'description' => 'Actif 30 jours consécutifs. Un mois complet !', 'icon' => 'lucide:flame', 'points' => 200, 'category' => AchievementCategory::STREAK],
            ['code' => 'streak_60', 'name' => 'Série de 60 jours', 'description' => 'Actif 60 jours consécutifs. Impressionnant !', 'icon' => 'lucide:crown', 'points' => 400, 'category' => AchievementCategory::STREAK],
            ['code' => 'streak_90', 'name' => 'Série de 90 jours', 'description' => '90 jours consécutifs. Légendaire !', 'icon' => 'lucide:crown', 'points' => 600, 'category' => AchievementCategory::STREAK],

            // --- IMC ---
            ['code' => 'bmi_below_40', 'name' => 'Sortie d\'obésité morbide', 'description' => 'Votre IMC est passé sous 40.', 'icon' => 'lucide:heart-pulse', 'points' => 200, 'category' => AchievementCategory::BMI],
            ['code' => 'bmi_below_35', 'name' => 'Sortie d\'obésité sévère', 'description' => 'Votre IMC est passé sous 35.', 'icon' => 'lucide:heart-pulse', 'points' => 300, 'category' => AchievementCategory::BMI],
            ['code' => 'bmi_below_30', 'name' => 'Sortie d\'obésité', 'description' => 'Votre IMC est passé sous 30. Surpoids atteint.', 'icon' => 'lucide:heart-pulse', 'points' => 500, 'category' => AchievementCategory::BMI],
            ['code' => 'bmi_below_25', 'name' => 'Poids normal', 'description' => 'Votre IMC est passé sous 25. Objectif atteint !', 'icon' => 'lucide:star', 'points' => 1000, 'category' => AchievementCategory::BMI],

            // --- RÉGULARITÉ ---
            ['code' => 'perfect_week', 'name' => 'Semaine parfaite', 'description' => 'Au moins une activité enregistrée chaque jour de la semaine.', 'icon' => 'lucide:calendar-check', 'points' => 75, 'category' => AchievementCategory::CONSISTENCY],
            ['code' => 'first_weekly_review', 'name' => 'Premier bilan', 'description' => 'Complétez votre premier bilan hebdomadaire.', 'icon' => 'lucide:clipboard-check', 'points' => 20, 'category' => AchievementCategory::CONSISTENCY],
        ];

        foreach ($achievements as $data) {
            Achievement::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $data['icon'],
                    'points' => $data['points'],
                    'category' => $data['category']->value,
                ]
            );
        }
    }
}
