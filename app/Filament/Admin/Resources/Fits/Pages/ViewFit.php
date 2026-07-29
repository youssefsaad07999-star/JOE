<?php

namespace App\Filament\Admin\Resources\Fits\Pages;

use App\Filament\Admin\Resources\Fits\FitResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFit extends ViewRecord
{
    protected static string $resource = FitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
