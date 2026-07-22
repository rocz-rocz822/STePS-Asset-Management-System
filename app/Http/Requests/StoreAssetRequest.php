<?php

namespace App\Http\Requests;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Asset::class);
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
            'serial_number' => ['nullable', 'string', 'max:150', 'unique:assets,serial_number'],
            'property_number' => ['nullable', 'string', 'max:100'],
            'inventory_number' => ['nullable', 'string', 'max:100'],
            'manufacturer' => ['nullable', 'string', 'max:150'],
            'supplier' => ['nullable', 'string', 'max:150'],
            'purchase_date' => ['nullable', 'date', 'before_or_equal:today'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'warranty_expiration' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'status' => ['required', new Enum(AssetStatus::class)],
            'condition' => ['required', new Enum(AssetCondition::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'remarks' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'max:4096'], // 4MB
            'attachments.*' => [
                'nullable',
                File::types(['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])->max(8 * 1024),
            ],
        ];
    }
}