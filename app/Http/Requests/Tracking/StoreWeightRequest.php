<?php

namespace App\Http\Requests\Tracking;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeightRequest extends FormRequest
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
            'weight' => 'required|numeric|min:20|max:500',
            'notes' => 'nullable|string|max:1000',
            'recorded_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'weight_kg.required' => 'Le poids est obligatoire',
            'weight_kg.numeric' => 'Le poids doit être un nombre',
            'weight_kg.min' => 'Le poids doit être supérieur à 20 kg',
            'weight_kg.max' => 'Le poids doit être inférieur à 500 kg',
        ];
    }
}
