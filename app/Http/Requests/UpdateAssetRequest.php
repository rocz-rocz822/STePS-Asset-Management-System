<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('asset'));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category_id' => ['required', 'exists:categories,id'],
            'location_id' => ['required', 'exists:locations,id'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],

            'serial_number' => [
                'nullable',
                'string',
                'max:150',
                Rule::unique('assets', 'serial_number')->ignore($this->route('asset')),
            ],

            // Property Number must be 000-000-000
            'property_number' => ['nullable', 'regex:/^\d{3}-\d{3}-\d{3}$/'],

            'manufacturer' => ['nullable', 'string', 'max:150'],
            'supplier' => ['nullable', 'string', 'max:150'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'warranty_expiration' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'status' => ['required', new Enum(AssetStatus::class)],
            'condition' => ['required', new Enum(AssetCondition::class)],

            'assigned_to' => [
                Rule::requiredIf(fn () => ! $this->user()->isAdmin()),
                'nullable',
                Rule::in(
                    $this->user()->assignableUsers()->pluck('id')
                        ->push($this->route('asset')->assigned_to)
                        ->filter()
                        ->unique()
                        ->all()
                ),
            ],

            'remarks' => ['nullable', 'string', 'max:2000'],

            'photo' => ['nullable', 'image', 'max:4096'],

            'attachments.*' => [
                'nullable',
                File::types(['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])->max(8 * 1024),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'property_number.regex' => 'Property Number must be in the format 000-000-000.',
            'assigned_to.required' => 'Please choose a user to assign this asset to.',
            'assigned_to.in' => 'You are not allowed to assign assets to that user.',
        ];
    }
}