<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBorrowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\BorrowRecord::class);
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'borrower_name' => ['required', 'string', 'max:150'],
            'borrower_department' => ['nullable', 'string', 'max:150'],
            'borrower_contact' => ['nullable', 'string', 'max:100'],
            'purpose' => ['nullable', 'string', 'max:1000'],
            'borrow_date' => ['required', 'date', 'before_or_equal:today'],
            'expected_return_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->user()->isAdmin() || ! $this->filled('asset_id')) {
                return;
            }

            $asset = Asset::find($this->asset_id);

            if ($asset && $asset->assigned_to !== $this->user()->id) {
                $validator->errors()->add('asset_id', 'You can only lend out assets assigned to you.');
            }
        });
    }
}