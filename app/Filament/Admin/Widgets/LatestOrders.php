<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestOrders extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Fetches a fast, lightweight slice of recent checkout logs
                Order::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Order Ref')
                    ->formatStateUsing(fn ($state) => "#ORD-{$state}")
                    ->fontFamily('mono')
                    ->weight('semibold'),

                TextColumn::make('user.name')
                    ->label('Customer Account')
                    ->searchable()
                    ->default('Guest Checkout'),

                TextColumn::make('total_price')
                    ->label('Gross Amount')
                    ->money('EGP') // Safely handles backend cents notation
                    ->color('success'),
                // ->alignEnd(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Placed At')
                    ->dateTime('M d, Y @ H:i')
                    ->color('gray'),
            ])
            ->actions([
                // Quick link straight to the full fulfillment workbench view page
                Action::make('viewOrder')
                    ->label('Manage')
                    ->icon('heroicon-m-eye')
                    ->button()
                    ->size('sm')
                    ->color('gray')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record)),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
