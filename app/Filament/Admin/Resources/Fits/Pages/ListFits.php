<?php

namespace App\Filament\Admin\Resources\Fits\Pages;

use App\Filament\Admin\Resources\Fits\FitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFits extends ListRecords
{
    protected static string $resource = FitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
