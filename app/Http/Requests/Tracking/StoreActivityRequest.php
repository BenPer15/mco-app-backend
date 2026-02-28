<?php

namespace App\Http\Requests\Tracking;

use App\Domains\Tracking\Enums\ActivityType;
use App\Domains\Tracking\Enums\IntensityLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $patient = $this->route('patient');
        return $patient && $patient->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity_type' => ['required', Rule::enum(ActivityType::class)],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'intensity_level' => ['required', Rule::enum(IntensityLevel::class)],
            'borg_rating' => ['nullable', 'integer', 'min:6', 'max:20'],
            'number_of_steps' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'recorded_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'activity_type.required' => 'Le type d\'activité est obligatoire',
            'duration_minutes.required' => 'La durée est obligatoire',
            'duration_minutes.min' => 'La durée doit être d\'au moins 1 minute',
            'intensity_level.required' => 'Le niveau d\'intensité est obligatoire',
            'borg_rating.min' => 'L\'échelle de Borg va de 6 à 20',
            'borg_rating.max' => 'L\'échelle de Borg va de 6 à 20',
        ];
    }
}
