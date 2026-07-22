<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('location'));
    }

    public function rules(): array
    {
        return [
            'building' => ['required', 'string', 'max:100'],
            'floor' => ['nullable', 'string', 'max:50'],
            'room' => ['nullable', 'string', 'max:100'],
            'storage_area' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ];
    }
}