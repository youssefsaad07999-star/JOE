<?php

namespace App\Filament\Admin\Resources\Colors;

use App\Filament\Admin\Resources\Colors\Pages\CreateColor;
use App\Filament\Admin\Resources\Colors\Pages\EditColor;
use App\Filament\Admin\Resources\Colors\Pages\ListColors;
use App\Filament\Admin\Resources\Colors\Pages\ViewColor;
use App\Filament\Admin\Resources\Colors\Schemas\ColorForm;
use App\Filament\Admin\Resources\Colors\Schemas\ColorInfolist;
use App\Filament\Admin\Resources\Colors\Tables\ColorsTable;
use App\Models\Color;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ColorResource extends Resource
{
    protected static ?string $model = Color::class;

    protected static string|UnitEnum|null $navigationGroup = 'Attributes';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ColorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ColorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ColorsTable::configure($table);
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
            'index' => ListColors::route('/'),
            // 'create' => CreateColor::route('/create'),
            // 'view' => ViewColor::route('/{record}'),
            // 'edit' => EditColor::route('/{record}/edit'),
        ];
    }
}
