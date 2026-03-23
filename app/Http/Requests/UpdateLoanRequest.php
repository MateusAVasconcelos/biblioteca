<?php

namespace App\Http\Requests;

use App\Enums\LoanStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer', 'exists:clients,id'],
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'status' => ['required', Rule::enum(LoanStatus::class)],
        ];
    }
}
