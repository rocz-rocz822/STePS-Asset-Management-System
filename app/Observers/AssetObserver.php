<?php

namespace App\Observers;

use App\Enums\AssetHistoryAction;
use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

class AssetObserver
{
    /**
     * Fields we never want to show up as "changes" in the history log.
     */
    private const IGNORED_FIELDS = [
        'updated_at',
        'updated_by',
        'photo_path',
    ];

    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset): void
    {
        // Generate Asset Code
        $asset->updateQuietly([
            'asset_code' => 'STEPS-IT-' . str_pad($asset->id, 6, '0', STR_PAD_LEFT),
        ]);

        // Record history
        $this->recordHistory(
            $asset,
            AssetHistoryAction::Created,
            'Asset record created.'
        );
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        $changes = collect($asset->getChanges())
            ->except(self::IGNORED_FIELDS)
            ->filter(fn ($value, $field) => $asset->getOriginal($field) !== $value);

        if ($changes->isEmpty()) {
            return;
        }

        $remarks = RequestFacade::input('change_remarks');

        foreach ($changes as $field => $newValue) {
            AssetHistory::create([
                'asset_id'          => $asset->id,
                'action'            => AssetHistoryAction::Updated->value,
                'user_id'           => Auth::id(),
                'performed_by_name' => Auth::user()?->name ?? 'System',
                'changed_field'     => $field,
                'old_value'         => $asset->getOriginal($field),
                'new_value'         => $newValue,
                'remarks'           => $remarks,
            ]);
        }
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset): void
    {
        $this->recordHistory(
            $asset,
            AssetHistoryAction::Deleted,
            'Asset moved to trash.'
        );
    }

    /**
     * Handle the Asset "restored" event.
     */
    public function restored(Asset $asset): void
    {
        $this->recordHistory(
            $asset,
            AssetHistoryAction::Restored,
            'Asset restored from trash.'
        );
    }

    /**
     * Record asset history.
     */
    private function recordHistory(
        Asset $asset,
        AssetHistoryAction $action,
        string $remarks
    ): void {
        AssetHistory::create([
            'asset_id'          => $asset->id,
            'action'            => $action->value,
            'user_id'           => Auth::id(),
            'performed_by_name' => Auth::user()?->name ?? 'System',
            'remarks'           => $remarks,
        ]);
    }
}