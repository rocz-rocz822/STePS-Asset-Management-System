<?php

namespace App\Models;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'asset_code',
        'name',
        'description',
        'category_id',
        'location_id',
        'brand',
        'model',
        'serial_number',
        'property_number',
        'inventory_number',
        'manufacturer',
        'supplier',
        'purchase_date',
        'purchase_cost',
        'warranty_expiration',
        'status',
        'condition',
        'assigned_to',
        'remarks',
        'photo_path',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expiration' => 'date',
            'purchase_cost' => 'decimal:2',
            'status' => AssetStatus::class,
            'condition' => AssetCondition::class,
        ];
    }

    /**
     * Automatically generate Asset Code
     */
    protected static function booted(): void
    {
        static::creating(function (Asset $asset) {

            if (! empty($asset->asset_code)) {
                return;
            }

            $lastId = self::withTrashed()->max('id') ?? 0;

            $nextNumber = $lastId + 1;

            $asset->asset_code = 'STEPS-IT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'status',
                'condition',
                'category_id',
                'location_id',
                'assigned_to',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('asset')
            ->setDescriptionForEvent(
                fn (string $eventName) => "Asset {$this->asset_code} was {$eventName}"
            );
    }

    public function tapActivity(Activity $activity, string $eventName): void
    {
        $activity->properties = $activity->properties->merge([
            'ip_address' => request()->ip(),
            'browser' => request()->userAgent(),
            'asset_code' => $this->asset_code,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AssetHistory::class)->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isUnderWarranty(): bool
    {
        return $this->warranty_expiration &&
               $this->warranty_expiration->isFuture();
    }

    public function warrantyExpiringSoon(int $days = 30): bool
    {
        return $this->warranty_expiration
            && $this->warranty_expiration->isFuture()
            && $this->warranty_expiration->diffInDays(now()) <= $days;
    }

    /*
    |--------------------------------------------------------------------------
    | Search Scope
    |--------------------------------------------------------------------------
    */

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('asset_code', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('model', 'like', "%{$term}%")
                ->orWhere('serial_number', 'like', "%{$term}%")
                ->orWhere('property_number', 'like', "%{$term}%")
                ->orWhere('inventory_number', 'like', "%{$term}%");
        });
    }

    public function borrowRecords(): HasMany
    {
        return $this->hasMany(BorrowRecord::class)->latest('borrow_date');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class)->latest('maintenance_date');
    }

    public function activeBorrow(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BorrowRecord::class)->where('status', 'borrowed')->latestOfMany('borrow_date');
    }
}