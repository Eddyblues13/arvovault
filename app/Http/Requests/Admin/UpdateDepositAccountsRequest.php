<?php

namespace App\Http\Requests\Admin;

use App\PayoutMethod;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDepositAccountsRequest extends FormRequest
{
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
     * The form holds one group of fields per payment method, keyed by the method.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $methods = array_map(fn (PayoutMethod $method): string => $method->value, PayoutMethod::cases());

        return [
            'accounts' => ['required', 'array:'.implode(',', $methods)],
            'accounts.*.account_details' => ['nullable', 'string', 'max:255', 'required_if_accepted:accounts.*.is_active'],
            'accounts.*.instructions' => ['nullable', 'string', 'max:1000'],
            'accounts.*.is_active' => ['boolean'],
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
            'accounts.*.account_details.required_if_accepted' => 'Enter where users should send the money before accepting deposits with this method.',
        ];
    }
}
