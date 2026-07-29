<?php

namespace App\Filament\Admin\Resources\ShippingMethods\Pages;

use App\Filament\Admin\Resources\ShippingMethods\ShippingMethodResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewShippingMethod extends ViewRecord
{
    protected static string $resource = ShippingMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
