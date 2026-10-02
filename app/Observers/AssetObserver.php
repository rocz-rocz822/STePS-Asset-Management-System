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
        $asset->updateQuietly([
            'asset_code' => sprintf('%010d', $asset->id),
        ]);

        $this->recordHistory(
            $asset,
            AssetHistoryAction::Created,
            remarks: 'Asset record created.'
        );

        // Record the initial assignment separately.
        if ($asset->assigned_to) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => AssetHistoryAction::Created->value,
                'user_id' => Auth::id(),
                'performed_by_name' => Auth::user()?->name ?? 'System',
                'changed_field' => 'assigned_to',
                'old_value' => null,
                'new_value' => $asset->assigned_to,
                'remarks' => 'Assigned when the asset was added.',
            ]);
        }
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset): void
    {
        $changes = collect($asset->getChanges())
            ->except(self::IGNORED_FIELDS)
            ->filter(
                fn ($value, $field) =>
                    $asset->getOriginal($field) !== $value
            );

        if ($changes->isEmpty()) {
            return;
        }

        $remarks = RequestFacade::input('change_remarks');

        foreach ($changes as $field => $newValue) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'action' => AssetHistoryAction::Updated->value,
                'user_id' => Auth::id(),
                'performed_by_name' => Auth::user()?->name ?? 'System',
                'changed_field' => $field,
                'old_value' => $asset->getOriginal($field),
                'new_value' => $newValue,
                'remarks' => $remarks,
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
            'asset_id' => $asset->id,
            'action' => $action->value,
            'user_id' => Auth::id(),
            'performed_by_name' => Auth::user()?->name ?? 'System',
            'remarks' => $remarks,
        ]);
    }
}