<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpgradeBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * For now, we will allow any authenticated user to make this request.
     * Later, we could add logic to ensure the user owns the village.
     */
    public function authorize(): bool
    {
        return true; // Or auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return),
            ];
    }

    /**
     * Prepare the data for validation.
     * This method allows us to merge the route parameter 'building'
     * into the request data so it can be validated by the rules() method.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'building' => $this->route('building'),
        ]);
    }
}