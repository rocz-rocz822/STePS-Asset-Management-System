<?php

namespace App\Http\Requests;

use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ForceAssetStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('forceStatus', $this->route('asset'));
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(AssetStatus::class)],
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'A reason is required when force-changing an asset status.',
        ];
    }
}