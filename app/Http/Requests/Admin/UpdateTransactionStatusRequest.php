<?php

namespace App\Http\Requests\Admin;

use App\TransactionStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionStatusRequest extends FormRequest
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
            'status' => ['required', Rule::enum(TransactionStatus::class)->only([TransactionStatus::Completed, TransactionStatus::Failed])],
        ];
    }
}
