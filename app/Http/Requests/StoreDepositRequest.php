<?php

namespace App\Http\Requests;

use App\Models\DepositAccount;
use App\PayoutMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepositRequest extends FormRequest
{
    /**
     * The smallest amount, in dollars, that can be deposited.
     */
    public const MIN_AMOUNT = 1;

    /**
     * The largest amount, in dollars, that can be deposited in one request.
     */
    public const MAX_AMOUNT = 1_000_000;

    /**
     * Determine if the user is authorized to make this request.
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
            'method' => [
                'required',
                Rule::enum(PayoutMethod::class),
                Rule::exists(DepositAccount::class, 'method')->where('is_active', true)->whereNotNull('account_details'),
            ],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:'.self::MIN_AMOUNT, 'max:'.self::MAX_AMOUNT],
            'reference' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'method.exists' => 'Deposits with this method are not available right now.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'reference' => 'payment reference',
        ];
    }

    /**
     * Get the payment method the user chose, when it is a valid one.
     */
    public function payoutMethod(): ?PayoutMethod
    {
        return $this->enum('method', PayoutMethod::class);
    }

    /**
     * Get the validated amount in cents.
     */
    public function amountInCents(): int
    {
        return (int) round((float) $this->validated('amount') * 100);
    }
}
