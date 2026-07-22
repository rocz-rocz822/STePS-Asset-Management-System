<?php

namespace App\Services;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\BorrowRecord;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportService
{
    public function generate(string $type, Request $request): array
    {
        return match ($type) {
            'complete_inventory' => $this->completeInventory($request),
            'by_category' => $this->byCategory($request),
            'by_status' => $this->byStatus($request),
            'by_condition' => $this->byCondition($request),
            'by_location' => $this->byLocation($request),
            'warranty_expiration' => $this->warrantyExpiration($request),
            'borrowing_history' => $this->borrowingHistory($request),
            'maintenance_history' => $this->maintenanceHistory($request),
            'asset_history' => $this->assetHistory($request),
            'recently_added' => $this->recentlyAdded($request),
            default => abort(404, 'Unknown report type.'),
        };
    }

    private function dateFilter($query, Request $request, string $column)
    {
        return $query
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate($column, '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate($column, '<=', $request->date_to));
    }

    private function completeInventory(Request $request): array
    {
        $assets = $this->dateFilter(Asset::with(['category', 'location']), $request, 'purchase_date')
            ->orderBy('asset_code')->get();

        return [
            'title' => 'Complete Inventory Report',
            'headers' => ['Asset Code', 'Name', 'Category', 'Location', 'Status', 'Condition', 'Purchase Date', 'Cost'],
            'rows' => $assets->map(fn ($a) => [
                $a->asset_code, $a->name, $a->category->name, $a->location->full_name,
                $a->status->label(), $a->condition->label(),
                $a->purchase_date?->format('M d, Y') ?? '—',
                $a->purchase_cost ? number_format($a->purchase_cost, 2) : '—',
            ]),
        ];
    }

    private function byCategory(Request $request): array
    {
        $assets = $this->dateFilter(Asset::with(['category', 'location']), $request, 'purchase_date')
            ->orderBy('category_id')->orderBy('name')->get();

        return [
            'title' => 'Assets by Category Report',
            'headers' => ['Category', 'Asset Code', 'Name', 'Status', 'Location'],
            'rows' => $assets->map(fn ($a) => [$a->category->name, $a->asset_code, $a->name, $a->status->label(), $a->location->full_name]),
        ];
    }

    private function byStatus(Request $request): array
    {
        $assets = $this->dateFilter(Asset::with(['category', 'location']), $request, 'purchase_date')
            ->orderBy('status')->orderBy('name')->get();

        return [
            'title' => 'Assets by Status Report',
            'headers' => ['Status', 'Asset Code', 'Name', 'Category', 'Location'],
            'rows' => $assets->map(fn ($a) => [$a->status->label(), $a->asset_code, $a->name, $a->category->name, $a->location->full_name]),
        ];
    }

    private function byCondition(Request $request): array
    {
        $assets = $this->dateFilter(Asset::with(['category', 'location']), $request, 'purchase_date')
            ->orderBy('condition')->orderBy('name')->get();

        return [
            'title' => 'Assets by Condition Report',
            'headers' => ['Condition', 'Asset Code', 'Name', 'Category', 'Location'],
            'rows' => $assets->map(fn ($a) => [$a->condition->label(), $a->asset_code, $a->name, $a->category->name, $a->location->full_name]),
        ];
    }

    private function byLocation(Request $request): array
    {
        $assets = $this->dateFilter(Asset::with(['category', 'location']), $request, 'purchase_date')
            ->orderBy('location_id')->orderBy('name')->get();

        return [
            'title' => 'Assets by Location Report',
            'headers' => ['Location', 'Asset Code', 'Name', 'Category', 'Status'],
            'rows' => $assets->map(fn ($a) => [$a->location->full_name, $a->asset_code, $a->name, $a->category->name, $a->status->label()]),
        ];
    }

    private function warrantyExpiration(Request $request): array
    {
        $assets = Asset::with(['category', 'location'])
            ->whereNotNull('warranty_expiration')
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('warranty_expiration', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('warranty_expiration', '<=', $request->date_to))
            ->orderBy('warranty_expiration')
            ->get();

        return [
            'title' => 'Warranty Expiration Report',
            'headers' => ['Asset Code', 'Name', 'Category', 'Warranty Expiration', 'Status'],
            'rows' => $assets->map(fn ($a) => [
                $a->asset_code, $a->name, $a->category->name,
                $a->warranty_expiration->format('M d, Y'),
                $a->isUnderWarranty() ? ($a->warrantyExpiringSoon() ? 'Expiring Soon' : 'Active') : 'Expired',
            ]),
        ];
    }

    private function borrowingHistory(Request $request): array
    {
        $records = $this->dateFilter(BorrowRecord::with('asset'), $request, 'borrow_date')
            ->latest('borrow_date')->get();

        return [
            'title' => 'Borrowing History Report',
            'headers' => ['Asset Code', 'Borrower', 'Department', 'Borrow Date', 'Expected Return', 'Actual Return', 'Status'],
            'rows' => $records->map(fn ($r) => [
                $r->asset->asset_code, $r->borrower_name, $r->borrower_department ?? '—',
                $r->borrow_date->format('M d, Y'), $r->expected_return_date->format('M d, Y'),
                $r->actual_return_date?->format('M d, Y') ?? '—', $r->status->label(),
            ]),
        ];
    }

    private function maintenanceHistory(Request $request): array
    {
        $records = $this->dateFilter(MaintenanceRecord::with('asset'), $request, 'maintenance_date')
            ->latest('maintenance_date')->get();

        return [
            'title' => 'Maintenance History Report',
            'headers' => ['Asset Code', 'Date', 'Technician', 'Issue', 'Cost', 'Status'],
            'rows' => $records->map(fn ($r) => [
                $r->asset->asset_code, $r->maintenance_date->format('M d, Y'), $r->technician_name,
                Str::limit($r->issue, 60), $r->cost ? number_format($r->cost, 2) : '—', $r->status->label(),
            ]),
        ];
    }

    private function assetHistory(Request $request): array
    {
        $query = AssetHistory::with(['asset']);

        if (! $request->user()->isAdmin()) {
            $query->whereHas('asset', fn ($q) => $q->where('created_by', $request->user()->id));
        }

        $histories = $this->dateFilter($query, $request, 'created_at')->latest('created_at')->get();

        return [
            'title' => 'Asset History Report',
            'headers' => ['Asset Code', 'Action', 'Field', 'Old Value', 'New Value', 'By', 'Date'],
            'rows' => $histories->map(fn ($h) => [
                $h->asset->asset_code ?? '—', $h->action->label(), $h->field_label ?? '—',
                $h->display_old_value ?? '—', $h->display_new_value ?? '—',
                $h->performed_by_name, $h->created_at->format('M d, Y g:ia'),
            ]),
        ];
    }

    private function recentlyAdded(Request $request): array
    {
        $assets = $this->dateFilter(Asset::with(['category', 'location']), $request, 'created_at')
            ->latest('created_at')
            ->when(! $request->filled('date_from') && ! $request->filled('date_to'), fn ($q) => $q->take(50))
            ->get();

        return [
            'title' => 'Recently Added Assets Report',
            'headers' => ['Asset Code', 'Name', 'Category', 'Location', 'Added On'],
            'rows' => $assets->map(fn ($a) => [$a->asset_code, $a->name, $a->category->name, $a->location->full_name, $a->created_at->format('M d, Y')]),
        ];
    }
}