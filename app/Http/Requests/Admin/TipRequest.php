<?php

namespace App\Http\Requests\Admin;

use App\Models\Tip;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TipRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:10000'],
            'is_published' => ['boolean'],
        ];
    }

    /**
     * Get the tip's attributes from the validated input.
     *
     * A tip that is already published keeps its original publish date.
     *
     * @return array{title: string, body: string, published_at: mixed}
     */
    public function tipAttributes(?Tip $tip = null): array
    {
        return [
            'title' => $this->validated('title'),
            'body' => $this->validated('body'),
            'published_at' => $this->boolean('is_published') ? ($tip?->published_at ?? now()) : null,
        ];
    }
}
