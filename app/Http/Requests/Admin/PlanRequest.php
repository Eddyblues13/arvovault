<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class PlanRequest extends FormRequest
{
    /**
     * The highest price, in dollars, a plan can have.
     */
    public const MAX_PRICE = 1_000_000;

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
            'name' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:'.self::MAX_PRICE],
            'description' => ['nullable', 'string', 'max:1000'],
            'features' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get the plan's attributes from the validated input.
     *
     * The features box holds one feature per line.
     *
     * @return array{name: string, price_in_cents: int, description: ?string, features: list<string>, is_active: bool}
     */
    public function planAttributes(): array
    {
        return [
            'name' => $this->validated('name'),
            'price_in_cents' => (int) round((float) $this->validated('price') * 100),
            'description' => $this->validated('description'),
            'features' => Str::of((string) $this->validated('features'))
                ->explode("\n")
                ->map(fn (string $feature): string => trim($feature))
                ->filter()
                ->values()
                ->all(),
            'is_active' => $this->boolean('is_active'),
        ];
    }
}
