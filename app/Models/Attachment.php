<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class Attachment extends Model
{
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getHumanSizeAttribute(): string
    {
        return Number::fileSize($this->file_size);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }
}