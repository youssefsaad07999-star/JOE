<?php

namespace App\Filament\Admin\Resources\Colors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ColorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')

                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->weight('semibold'),

                ColorColumn::make('hex_code')
                    ->label('Color Preview')
                    ->copyable() // Admins can click the pill to copy the hex code instantly
                    ->copyMessage('Hex code copied to clipboard'),

                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Active Variants Matching')
                    ->badge()
                    ->color('gray'),
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
            ->recordActions([
                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
