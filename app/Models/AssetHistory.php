<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetHistoryAction;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetHistory extends Model
{
    const UPDATED_AT = null; // history rows are immutable — no updated_at

    protected $fillable = [
        'asset_id',
        'action',
        'user_id',
        'performed_by_name',
        'changed_field',
        'old_value',
        'new_value',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'action' => AssetHistoryAction::class,
            'created_at' => 'datetime',
        ];
    }

    /**
     * Human-readable label for the changed field.
     * e.g. "category_id" -> "Category", "purchase_cost" -> "Purchase Cost"
     */
    private const FIELD_LABELS = [
        'category_id' => 'Category',
        'location_id' => 'Location',
        'assigned_to' => 'Assigned User',
        'purchase_cost' => 'Purchase Cost',
        'purchase_date' => 'Purchase Date',
        'warranty_expiration' => 'Warranty Expiration',
        'serial_number' => 'Serial Number',
        'property_number' => 'Property Number',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFieldLabelAttribute(): ?string
    {
        if (! $this->changed_field) {
            return null;
        }

        return self::FIELD_LABELS[$this->changed_field] ?? ucwords(str_replace('_', ' ', $this->changed_field));
    }

    public function getDisplayOldValueAttribute(): ?string
    {
        return $this->formatValue($this->changed_field, $this->old_value);
    }

    public function getDisplayNewValueAttribute(): ?string
    {
        return $this->formatValue($this->changed_field, $this->new_value);
    }

    private function formatValue(?string $field, ?string $value): ?string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return match ($field) {
            'category_id' => Category::find($value)?->name ?? "#{$value}",
            'location_id' => Location::find($value)?->full_name ?? "#{$value}",
            'assigned_to' => User::find($value)?->name ?? "#{$value}",
            'status' => AssetStatus::tryFrom($value)?->label() ?? $value,
            'condition' => AssetCondition::tryFrom($value)?->label() ?? $value,
            'purchase_cost' => '₱'.number_format((float) $value, 2),
            'purchase_date', 'warranty_expiration' => \Illuminate\Support\Carbon::parse($value)->format('M d, Y'),
            default => $value,
        };
    }
}