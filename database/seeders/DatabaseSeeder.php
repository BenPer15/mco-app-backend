<?php

namespace Database\Seeders;

use App\Domains\Core\Models\User;
use App\Domains\Patient\Enums\Gender;
use App\Domains\Patient\Enums\SurgeryType;
use App\Domains\Patient\Models\Patient;
use App\Domains\Tracking\Services\WeightTrackingService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'email' => 'benoit.peron62@gmail.com',
        ]);

        $patient = Patient::factory()->create([
            'user_id' => $user->id,
            'first_name' => 'Benoit',
            'last_name' => 'Peron',
            'birth_date' => '1988-12-15',
            'gender' => Gender::MALE->value,
            'surgery_type' => SurgeryType::BYPASS->value,
            'surgery_date' => now()->addMonths(4)->format('Y-m-d'),
            'height_cm' => 179,
            'settings' => [
                'objectives' => [
                    'steps' => 8000,
                    'activities' =>  60,
                ]
            ]
        ]);


        $service = app(WeightTrackingService::class);

        $service->recordWeight($patient, [
            'weight' => 170,
            'recorded_at' => now()->subDays(30)->format('Y-m-d H:i:s'),
        ]);

        $this->call(AchievementSeeder::class);
    }
}
