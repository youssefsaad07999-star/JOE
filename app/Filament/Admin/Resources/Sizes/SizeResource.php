<?php

namespace App\Filament\Admin\Resources\Sizes;

use App\Filament\Admin\Resources\Sizes\Pages\CreateSize;
use App\Filament\Admin\Resources\Sizes\Pages\EditSize;
use App\Filament\Admin\Resources\Sizes\Pages\ListSizes;
use App\Filament\Admin\Resources\Sizes\Pages\ViewSize;
use App\Filament\Admin\Resources\Sizes\Schemas\SizeForm;
use App\Filament\Admin\Resources\Sizes\Schemas\SizeInfolist;
use App\Filament\Admin\Resources\Sizes\Tables\SizesTable;
use App\Models\Size;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class SizeResource extends Resource
{
    protected static ?string $model = Size::class;

    protected static string|UnitEnum|null $navigationGroup = 'Attributes';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-scale';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SizeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SizeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SizesTable::configure($table);
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
            'index' => ListSizes::route('/'),
            'create' => CreateSize::route('/create'),
            'view' => ViewSize::route('/{record}'),
            'edit' => EditSize::route('/{record}/edit'),
        ];
    }
}
