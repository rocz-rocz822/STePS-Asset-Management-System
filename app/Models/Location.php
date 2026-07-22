<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'building',
        'floor',
        'room',
        'storage_area',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Human-readable full path, e.g. "Main Building / 3rd Floor / IT Room / Cabinet A"
     */
    public function getFullNameAttribute(): string
    {
        return collect([$this->building, $this->floor, $this->room, $this->storage_area])
            ->filter()
            ->implode(' / ');
    }

    public function assets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Asset::class);
    }
}