<?php

namespace App\Http\Controllers\Api\Admin\Concerns;

use App\Models\PackageItem;
use App\Models\TripItem;

trait ProtectsCatalogItemDeletion
{
    private function abortIfCatalogItemIsUsed(string $itemType, int $itemId): void
    {
        abort_if(
            PackageItem::where('item_type', $itemType)->where('item_id', $itemId)->exists()
                || TripItem::where('item_type', $itemType)->where('item_id', $itemId)->exists(),
            409,
            'Impossible de supprimer cet element, car il est utilise dans un package ou un trip.'
        );
    }
}
