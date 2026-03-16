<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Income;

class UpdateIncomeRequest extends FormRequest
{
    /**
     * Authorize request
     */
    public function authorize(): bool
    {
        $income = $this->route('income');

        // If using Route Model Binding
        if ($income instanceof Income) {
            return $income->user_id === Auth::id();
        }

        return false;
    }

    /**
     * Prepare data before validation
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'income_date' => $this->income_date ?? now(),
        ]);
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
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
     * Custom messages
     */
    public function messages(): array
    {
        return [
            'amount.min' => 'Income must be at least ₹1.',
            'amount.max' => 'Income amount exceeds allowed limit.',
            'income_date.before_or_equal' => 'Income date cannot be in the future.',
        ];
    }

    /**
     * Clean validated data
     */
    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        $data['income_date'] = $data['income_date'] ?? now();

        return $data;
    }
}
