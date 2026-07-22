<?php

namespace App\Models;

use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'maintenance_date',
        'technician_id',
        'technician_name',
        'issue',
        'resolution',
        'cost',
        'status',
        'resulting_asset_status',
        'previous_asset_status',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_date' => 'date',
            'cost' => 'decimal:2',
            'status' => MaintenanceStatus::class,
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('issue', 'like', "%{$term}%")
                ->orWhere('technician_name', 'like', "%{$term}%")
                ->orWhereHas('asset', fn ($aq) => $aq->where('asset_code', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"));
        });
    }
}