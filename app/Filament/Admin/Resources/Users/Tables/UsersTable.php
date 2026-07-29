<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email copied to clipboard'),

                TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('orders_sum_total_price')
                    ->sum('orders', 'total_price')
                    ->label('Total Spent')
                    ->money('EGP')
                    ->placeholder('No purchases')
                    ->weight('medium')
                    ->color(fn ($state) => $state > 2000 ? 'success' : 'gray')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Joined Date')
                    ->date('M d, Y')
                    ->description(fn ($record) => $record->created_at?->diffForHumans())
                    ->sortable()
                    ->color('gray')
                    ->size('sm'),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),

                Action::make('toggleRole')
    // 1. Check against the standard 'admin' role
                    ->label(fn (User $record) => $record->hasRole(['admin', 'super_admin']) ? 'Revoke Admin' : 'Promote to Admin')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-shield-check')
                    ->color(fn (User $record) => $record->hasRole(['admin', 'super_admin']) ? 'danger' : 'success')
                    ->action(function (User $record) {
                        if ($record->hasRole(['admin', 'super_admin'])) {
                            $record->removeRole(['admin', 'super_admin']);
                        } else {
                            $record->assignRole('admin');
                        }
                    })
                    ->disabled(fn (User $record) => $record->id === auth()->id() ||           // Prevent self-demotion
                        $record->hasRole('super_admin')           // Protect the master Super Admin account from being modified here
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
