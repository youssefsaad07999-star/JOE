<?php

namespace App\Filament\Admin\Resources\Fits\Pages;

use App\Filament\Admin\Resources\Fits\FitResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditFit extends EditRecord
{
    protected static string $resource = FitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
