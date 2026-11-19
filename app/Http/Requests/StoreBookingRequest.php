<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * How many months ahead a booking can be requested.
     */
    public const MAX_MONTHS_AHEAD = 6;

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
            'scheduled_for' => ['required', 'date', 'after:now', 'before:'.now()->addMonths(self::MAX_MONTHS_AHEAD)->toDateTimeString()],
            'topic' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'scheduled_for.required' => 'Choose a date and time.',
            'scheduled_for.date' => 'Choose a valid date and time.',
            'scheduled_for.after' => 'Choose a date and time in the future.',
            'scheduled_for.before' => 'Choose a date within the next '.self::MAX_MONTHS_AHEAD.' months.',
        ];
    }
}
