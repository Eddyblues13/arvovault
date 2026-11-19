<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FundUserRequest extends FormRequest
{
    /**
     * The largest amount, in dollars, that can be added in one funding.
     */
    public const MAX_AMOUNT = 1_000_000;

    /**
     * Determine if the user is authorized to make this request.
     *
     * Only signed-in admins reach this request, through the admin middleware.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:'.self::MAX_AMOUNT],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validated amount in cents.
     */
    public function amountInCents(): int
    {
        return (int) round((float) $this->validated('amount') * 100);
    }
}
