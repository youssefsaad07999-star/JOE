<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use App\OrderStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3) // 1. Set top-level form grid to 3 columns
            ->components([

                // ── LEFT COLUMN (Main Details - Spans 2 Columns) ───────────
                Group::make()
                    ->schema([
                        Section::make('Order Information')
                            ->description('Assign customer, status, and shipping method.')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([
                                Select::make('user_id')
                                    ->label('Customer')
                                    ->relationship('user', 'name')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                                    ->searchable(['name', 'email'])
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(), // 👈 Span full width so long emails don't wrap awkwardly!

                                Select::make('status')
                                    ->label('Order Status')
                                    ->options(OrderStatus::class)
                                    ->native(false)
                                    ->required(),

                                Select::make('shipping_method_id')
                                    ->label('Shipping Method')
                                    ->relationship('shippingMethod', 'name')
                                    ->native(false)
                                    ->searchable(),

                                Select::make('address_id')
                                    ->label('Saved Customer Address')
                                    ->relationship('address', 'id')
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->address_line_1}, {$record->city}")
                                    ->searchable()
                                    ->placeholder('None (Using Manual Shipping Address below)')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        Section::make('Shipping Address')
                            ->description('Delivery recipient and destination details.')
                            ->icon('heroicon-o-truck')
                            ->collapsible()
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('shipping_first_name')->label('First Name')->required(),
                                    TextInput::make('shipping_last_name')->label('Last Name')->required(),
                                ]),

                                TextInput::make('shipping_address')->label('Street Address')->required(),
                                TextInput::make('shipping_address2')->label('Street Address Line 2')->nullable(),

                                Grid::make(3)->schema([
                                    TextInput::make('shipping_city')->label('City')->required(),
                                    TextInput::make('shipping_postal_code')->label('Postal / ZIP Code'),
                                    TextInput::make('shipping_country')->label('Country')->required(),
                                ]),

                                TextInput::make('shipping_phone')
                                    ->label('Recipient Phone')
                                    ->tel()
                                    ->prefixIcon('heroicon-m-phone')
                                    ->placeholder('+1 (555) 000-0000'),
                            ]),
                    ])
                    ->columnSpan(2), // 👈 Occupies 2/3 of the screen width

                // ── RIGHT COLUMN (Sidebar Summary - Spans 1 Column) ─────────
                Group::make()
                    ->schema([
                        Section::make('Payment & Pricing')
                            ->description('Financial breakdown')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                TextInput::make('total_price')
                                    ->label('Total Order Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$')
                                    ->placeholder('0.00'),

                                TextInput::make('shipping_cost')
                                    ->label('Shipping Cost')
                                    ->required()
                                    ->numeric()
                                    ->default(0.0)
                                    ->prefix('$')
                                    ->placeholder('0.00'),
                            ])
                            ->columns(1), // Stack inputs vertically in the sidebar
                    ])
                    ->columnSpan(1), // 👈 Occupies 1/3 of the screen width
            ]);
    }
}
