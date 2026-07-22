<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\BorrowStatus;

class UpdateBorrowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('borrow_record'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(BorrowStatus::class)],
            'actual_return_date' => ['nullable', 'date', 'required_if:status,returned', 'after_or_equal:borrow_date'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}