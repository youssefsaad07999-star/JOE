<?php

namespace App\Filament\Admin\Resources\Orders\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('color,size,sku')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->weight('bold')
                    ->description(fn ($record) => "SKU: {$record->sku}")
                    ->searchable(),

                TextColumn::make('color.name')
                    ->label('Color')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color('gray')
                    ->placeholder('-'),

                TextColumn::make('size.name')
                    ->label('Size')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->placeholder('-'),

                TextColumn::make('pivot.quantity')
                    ->label('Qty')
                    ->alignCenter()
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => "{$state}x"),

                TextColumn::make('pivot.unit_price')
                    ->label('Unit Price')
                    ->money('USD')
                    ->color('gray')
                    ->alignEnd(),

                TextColumn::make('pivot.subtotal')
                    ->label('Subtotal')
                    ->money('USD')
                    ->weight('bold')
                    ->alignEnd()
                    ->state(fn ($record) => $record->pivot->quantity * $record->pivot->unit_price),
            ])
            ->filters([])
            ->headerActions([

            ])
            ->actions([])
            ->bulkActions([]);
    }
}
