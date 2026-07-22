<?php

namespace App\Enums;

enum ReportType: string
{
    case CompleteInventory = 'complete_inventory';
    case ByCategory = 'by_category';
    case ByStatus = 'by_status';
    case ByCondition = 'by_condition';
    case ByLocation = 'by_location';
    case WarrantyExpiration = 'warranty_expiration';
    case BorrowingHistory = 'borrowing_history';
    case MaintenanceHistory = 'maintenance_history';
    case AssetHistoryReport = 'asset_history';
    case RecentlyAdded = 'recently_added';

    public function label(): string
    {
        return match ($this) {
            self::CompleteInventory => 'Complete Inventory',
            self::ByCategory => 'Assets by Category',
            self::ByStatus => 'Assets by Status',
            self::ByCondition => 'Assets by Condition',
            self::ByLocation => 'Assets by Location',
            self::WarrantyExpiration => 'Warranty Expiration',
            self::BorrowingHistory => 'Borrowing History',
            self::MaintenanceHistory => 'Maintenance History',
            self::AssetHistoryReport => 'Asset History',
            self::RecentlyAdded => 'Recently Added Assets',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::CompleteInventory => 'Full listing of every asset in the system.',
            self::ByCategory => 'Assets grouped and sorted by category.',
            self::ByStatus => 'Assets grouped and sorted by current status.',
            self::ByCondition => 'Assets grouped and sorted by physical condition.',
            self::ByLocation => 'Assets grouped and sorted by location.',
            self::WarrantyExpiration => 'Assets sorted by warranty expiration date.',
            self::BorrowingHistory => 'Complete record of all borrow transactions.',
            self::MaintenanceHistory => 'Complete record of all maintenance activity.',
            self::AssetHistoryReport => 'Field-level audit trail of asset changes.',
            self::RecentlyAdded => 'Most recently added assets.',
        };
    }
}