<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('maintenance_record'));
    }

    public function rules(): array
    {
        return [
            'maintenance_date' => ['required', 'date', 'before_or_equal:today'],
            'technician_id' => ['required', 'exists:users,id'],
            'issue' => ['required', 'string', 'max:2000'],
            'resolution' => ['nullable', 'string', 'max:2000'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'resulting_asset_status' => ['required_if:status,pending,in_progress', 'nullable', Rule::in(['under_maintenance', 'under_repair'])],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'attachments.*' => [
                'nullable',
                File::types(['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'])->max(8 * 1024),
            ],
        ];
    }
}