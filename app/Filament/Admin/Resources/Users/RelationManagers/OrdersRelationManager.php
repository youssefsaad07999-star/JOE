<?php

namespace App\Filament\Admin\Resources\Users\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    // public function form(Schema $schema): Schema
    // {
    //     return $schema
    //         ->components([
    //             TextInput::make('id,status,total_price')
    //                 ->required()
    //                 ->maxLength(255),
    //         ]);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order #')->fontFamily('mono'),
                TextColumn::make('status')->badge(),
                TextColumn::make('total_price')->money('USD'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                ViewAction::make()->url(fn ($record) => route('filament.admin.resources.orders.view', $record)),
            ]);
    }
}
