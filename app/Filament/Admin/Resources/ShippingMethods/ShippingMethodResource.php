<?php

namespace App\Filament\Admin\Resources\ShippingMethods;

use App\Filament\Admin\Resources\ShippingMethods\Pages\CreateShippingMethod;
use App\Filament\Admin\Resources\ShippingMethods\Pages\EditShippingMethod;
use App\Filament\Admin\Resources\ShippingMethods\Pages\ListShippingMethods;
use App\Filament\Admin\Resources\ShippingMethods\Pages\ViewShippingMethod;
use App\Filament\Admin\Resources\ShippingMethods\Schemas\ShippingMethodForm;
use App\Filament\Admin\Resources\ShippingMethods\Schemas\ShippingMethodInfolist;
use App\Filament\Admin\Resources\ShippingMethods\Tables\ShippingMethodsTable;
use App\Models\ShippingMethod;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class ShippingMethodResource extends Resource
{
    protected static ?string $model = ShippingMethod::class;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ShippingMethodForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ShippingMethodInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ShippingMethodsTable::configure($table);
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
            'index' => ListShippingMethods::route('/'),
            // 'create' => CreateShippingMethod::route('/create'),
            // 'view' => ViewShippingMethod::route('/{record}'),
            // 'edit' => EditShippingMethod::route('/{record}/edit'),
        ];
    }
}
