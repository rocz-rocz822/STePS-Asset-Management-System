<?php

namespace App\Models;

use App\Enums\BorrowStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'borrower_name',
        'borrower_department',
        'borrower_contact',
        'purpose',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'previous_asset_status',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'expected_return_date' => 'date',
            'actual_return_date' => 'date',
            'status' => BorrowStatus::class,
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOverdue(): bool
    {
        return $this->status === BorrowStatus::Borrowed
            && $this->expected_return_date->isPast();
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('borrower_name', 'like', "%{$term}%")
                ->orWhere('borrower_department', 'like', "%{$term}%")
                ->orWhereHas('asset', fn ($aq) => $aq->where('asset_code', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"));
        });
    }
}