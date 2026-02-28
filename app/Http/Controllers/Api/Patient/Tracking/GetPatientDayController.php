<?php

namespace App\Http\Controllers\Api\Patient\Tracking;

use App\Domains\Patient\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class GetPatientDayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Patient $patient, Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|date_format:Y-m-d'
        ]);

        $day = Carbon::parse($validated['day']);

        $patientDay = $patient->patientDays()
            ->whereBetween('date', [
                $day->startOfDay(),  // 00:00:00
                $day->endOfDay()     // 23:59:59
            ])
            ->first();

        if (!$patientDay) {
            $patientDay = $patient->addPatientDay($day);
        }

        return response()->json([
            'date' => $patientDay->date->toDateString(),
            'steps' => $patientDay->totalSteps(),
            'active_minutes' => $patientDay->totalActiveMinutes(),
            'is_complete' => $patientDay->isComplete(),
        ]);
    }
}
