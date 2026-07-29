<?php

namespace App\Filament\Admin\Resources\Sizes\Pages;

use App\Filament\Admin\Resources\Sizes\SizeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSize extends ViewRecord
{
    protected static string $resource = SizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
