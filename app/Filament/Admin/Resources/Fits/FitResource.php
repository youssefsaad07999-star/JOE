<?php

namespace App\Filament\Admin\Resources\Fits;

use App\Filament\Admin\Resources\Fits\Pages\CreateFit;
use App\Filament\Admin\Resources\Fits\Pages\EditFit;
use App\Filament\Admin\Resources\Fits\Pages\ListFits;
use App\Filament\Admin\Resources\Fits\Pages\ViewFit;
use App\Filament\Admin\Resources\Fits\Schemas\FitForm;
use App\Filament\Admin\Resources\Fits\Schemas\FitInfolist;
use App\Filament\Admin\Resources\Fits\Tables\FitsTable;
use App\Models\Fit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class FitResource extends Resource
{
    protected static ?string $model = Fit::class;

    protected static string|UnitEnum|null $navigationGroup = 'Attributes';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFits::route('/'),
            'create' => CreateFit::route('/create'),
            'view' => ViewFit::route('/{record}'),
            'edit' => EditFit::route('/{record}/edit'),
        ];
    }
}
