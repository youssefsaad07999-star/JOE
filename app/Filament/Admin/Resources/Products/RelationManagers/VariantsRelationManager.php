<?php

namespace App\Filament\Admin\Resources\Products\RelationManagers;

use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Models\Color;
use App\Models\Size;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        $updateSkuClosure = function (Get $get, Set $set) {
            $colorId = $get('color_id');
            $sizeId = $get('size_id');

            // Extract 3-letter uppercase prefix for Color
            $colorPrefix = '';
            if ($colorId) {
                $color = Color::find($colorId);
                $colorPrefix = $color ? strtoupper(substr($color->name, 0, 3)) : '';
            }

            // Extract 3-letter uppercase prefix for Size
            $sizePrefix = '';
            if ($sizeId) {
                $size = Size::find($sizeId);
                $sizePrefix = $size ? strtoupper(substr($size->name, 0, 3)) : '';
            }

            // Only generate SKU if at least one attribute is selected
            if ($colorPrefix || $sizePrefix) {
                $set('sku', "SKU-{$colorPrefix}-{$sizePrefix}");
            }
        };

        return $schema
            ->components([

                Select::make('color_id')
                    ->label('Color Attribute')
                    ->relationship('color', 'name')
                    ->required()
                    ->live()
                    ->getOptionLabelFromRecordUsing(fn ($record) => ucfirst($record->name))
                    ->afterStateUpdated($updateSkuClosure),

                Select::make('size_id')
                    ->label('Size Attribute')
                    ->relationship('size', 'name')
                    ->required()
                    ->live() // Essential: Tells Filament to instantly send changes back to the server
                    ->getOptionLabelFromRecordUsing(fn ($record) => ucfirst($record->name)) // Capitalizes size options too
                    ->afterStateUpdated($updateSkuClosure),

                TextInput::make('sku')
                    ->label('Unique SKU')
                    ->placeholder('AUTO-GENERATED')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('stock_quantity')
                    ->label('Physical Stock')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                TextInput::make('price_override')
                    ->label('Price Override')
                    ->prefix('$')
                    ->placeholder('Using Base')
                    ->nullable(),

                Toggle::make('is_active')
                    ->label('Live')
                    ->default(true)
                    ->inline(false),

            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('size.name')
                    ->label('Size'),
                TextEntry::make('color.name')
                    ->label('Color'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('stock_quantity')
                    ->numeric(),
                TextEntry::make('price_override')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('color,size,sku')
            ->columns([
                TextColumn::make('color.name')
                    ->label('Colour')
                    ->formatStateUsing(fn ($state, $record) => '<span style="display:inline-flex;align-items:center;gap:6px;">'
                        .'<span style="width:10px;height:10px;border-radius:50%;'
                        .'background:'.($record->color->hex_code ?? '#ccc').';'
                         .'flex-shrink:0;"></span>'
                         .htmlspecialchars(ucfirst($state) ?? '—')
                        .'</span>'
                    )
                    ->html()
                    ->searchable(),
                TextColumn::make('size.name')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'danger',
                        $state <= 5 => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('price_override')
                    ->label('Price Override')
                    ->money('USD')
                    ->default('Base price')
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    // {
    //     // Hide this relation manager if the admin is currently on the Edit page
    //     if ($pageClass === EditProduct::class) {
    //         return false;
    //     }

    //     return true;
    // }
}
