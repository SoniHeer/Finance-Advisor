<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreIncomeRequest extends FormRequest
{
    /**
     * Authorize request
     */
    public function authorize(): bool
    {
        // Only logged-in users can create income
        return Auth::check();
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'income_type' => $this->income_type ?? 'personal',
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'income_type' => 'required|in:personal,family',

            'family_id' => [
                'nullable',
                'required_if:income_type,family',
                'exists:families,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:999999999.99',
            ],

            'source' => [
                'required',
                'string',
                'max:100',
            ],

            'income_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'family_id.required_if' => 'Please select a family when adding family income.',
            'amount.min' => 'Income amount must be at least ₹1.',
            'income_date.before_or_equal' => 'Income date cannot be in the future.',
        ];
    }

    /**
     * Optional: Customize validated data
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        // Ensure default date if not provided
        $data['income_date'] = $data['income_date'] ?? now();

        return $data;
    }
}
