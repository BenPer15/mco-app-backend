<?php

namespace App\Http\Requests\Tracking;

use Illuminate\Foundation\Http\FormRequest;

class StoreStepsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $patient = $this->route('patient');
        return $patient && $patient->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'steps' => 'required|integer|min:0|max:200000',
            'recorded_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'steps.required' => 'Le nombre de pas est obligatoire',
            'steps.integer' => 'Le nombre de pas doit être un nombre entier',
            'steps.min' => 'Le nombre de pas ne peut pas être négatif',
            'steps.max' => 'Le nombre de pas semble trop élevé',
        ];
    }
}
