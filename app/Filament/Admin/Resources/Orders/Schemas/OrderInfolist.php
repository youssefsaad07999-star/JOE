<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Order Details Section ──
                Section::make('Order Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('id')
                            ->label('Order ID #')
                            ->fontFamily('mono'),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('total_price')
                            ->money('USD'),
                        TextEntry::make('user.name')
                            ->label('Customer Name'),
                        TextEntry::make('user.email')
                            ->label('Customer Email'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ]),

                // ── Shipping Details Section ──
                Section::make('Shipping & Delivery')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('shipping_address')
                            ->label('Delivery Address')
                            ->formatStateUsing(fn ($record) => implode(', ', array_filter([
                                $record->shipping_first_name.' '.$record->shipping_last_name,
                                $record->shipping_address,
                                $record->shipping_city,
                                $record->shipping_postal_code,
                                $record->shipping_country,
                            ])))
                            ->columnSpanFull(),
                        TextEntry::make('shippingmethod.name')
                            ->label('Method Used'),
                        TextEntry::make('shipping_cost')
                            ->money('USD')
                            ->label('Shipping Cost'),
                    ]),

                // ── Payment Details Section ──
                Section::make('Payment Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('payment.method')
                            ->label('Payment Method')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'card' => 'Credit Card',
                                'cash on delivery' => 'Cash on Delivery',
                                default => 'No Payment Record',
                            })
                            ->default('Paddle Checkout'),
                        TextEntry::make('payment.status')
                            ->badge()
                            ->label('Payment Status')
                            ->default('No Payment Record'),
                        TextEntry::make('payment.transaction_id')
                            ->label('Transaction ID')
                            ->fontFamily('mono')
                            ->copyable()
                            ->hidden(fn ($record) => ! $record->payment),
                    ]),
            ]);
    }
}
