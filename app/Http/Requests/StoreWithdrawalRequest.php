<?php

namespace App\Http\Requests;

use App\PayoutMethod;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWithdrawalRequest extends FormRequest
{
    /**
     * The smallest amount, in dollars, that can be withdrawn.
     */
    public const MIN_AMOUNT = 1;

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
     * The available balance is checked when the withdrawal is created, under a lock,
     * because it can change between validation and saving.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'method' => ['required', Rule::enum(PayoutMethod::class)],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:'.self::MIN_AMOUNT],
            'destination' => ['required', 'string', 'max:255', $this->destinationSuitsMethod(...)],
        ];
    }

    /**
     * Get the payout method the user chose, when it is a valid one.
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

    /**
     * Fail when the destination is not valid for the chosen payout method.
     */
    private function destinationSuitsMethod(string $attribute, mixed $value, Closure $fail): void
    {
        $method = $this->payoutMethod();

        if ($method !== null && is_string($value) && ! $method->isValidDestination($value)) {
            $fail($method->destinationInstructions());
        }
    }
}
