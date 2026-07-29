<?php

namespace App\Filament\Admin\Resources\Orders\RelationManagers;

use App\PaymentStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentRelationManager extends RelationManager
{
    protected static string $relationship = 'payment';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Details')
                    ->description('Manage payment transaction details and status.')
                    ->columnSpanFull()
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        // 1. Customer
                        Select::make('user_id')
                            ->label('Customer')
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->email})")
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required(),

                        // 2. Order Reference (Auto-filled by Relation Manager)
                        Select::make('order_id')
                            ->label('Order #')
                            ->relationship('order', 'id')
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('Associated automatically'),

                        // 3. Amount & Method
                        TextInput::make('amount')
                            ->label('Amount Paid')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('0.00'),

                        Select::make('method')
                            ->label('Payment Method')
                            ->options([
                                'card' => 'Credit / Debit Card',
                                'cash on delivery' => 'Cash on Delivery',
                            ])
                            ->native(false)
                            ->required(),

                        // 4. Status & Gateway Reference
                        Select::make('status')
                            ->label('Payment Status')
                            ->options(PaymentStatus::class)
                            ->native(false)
                            ->required(),

                        TextInput::make('transaction_id')
                            ->label('Transaction / Gateway ID')
                            ->placeholder('txn_01ksnft3cp3y...')
                            ->prefixIcon('heroicon-m-hashtag')
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── 1. Financial & Gateway Details ───────────────────
                Section::make('Payment & Gateway Overview')
                    ->description('Transaction details and financial breakdown')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Amount Paid')
                            ->money('USD')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),

                        TextEntry::make('status')
                            ->label('Payment Status')
                            ->badge(),

                        TextEntry::make('method')
                            ->label('Payment Method')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match (strtolower($state ?? '')) {
                                'cod', 'cash on delivery' => 'Cash on Delivery',
                                'card' => 'Credit Card',
                                default => strtoupper($state ?? 'N/A'),
                            })
                            ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                                'cod', 'cash' => 'warning',
                                'card' => 'success',
                                default => 'gray',
                            })
                            ->icon(fn (?string $state): string => match (strtolower($state ?? '')) {
                                'card' => 'heroicon-m-credit-card',
                                'cod', 'cash on delivery' => 'heroicon-m-banknotes',
                                default => 'heroicon-m-receipt-percent',
                            }),

                        TextEntry::make('transaction_id')
                            ->label('Transaction Ref ID')
                            ->fontFamily('mono')
                            ->weight('bold')
                            ->icon('heroicon-m-hashtag')
                            ->copyable()
                            ->copyMessage('Transaction ID copied')
                            ->placeholder('N/A (Local / Manual)'),
                    ])
                    ->columns(2),

                // ── 2. Relationships & Timestamps ──────────────────────
                Section::make('Relationships & Audit Log')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Customer')
                            ->weight('bold')
                            ->icon('heroicon-m-user')
                            ->helperText(fn ($record) => $record->user?->email)
                            ->placeholder('Guest / Unknown'),

                        TextEntry::make('order.id')
                            ->label('Associated Order')
                            ->prefix('#')
                            ->fontFamily('mono')
                            ->weight('bold')
                            ->icon('heroicon-m-shopping-bag')
                            ->placeholder('No linked order'),

                        TextEntry::make('created_at')
                            ->label('Payment Date')
                            ->dateTime('M d, Y H:i')
                            ->icon('heroicon-m-calendar')
                            ->helperText(fn ($record) => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M d, Y H:i')
                            ->color('gray')
                            ->helperText(fn ($record) => $record->updated_at?->diffForHumans()),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('transaction_id')
            ->columns([
                // 1. Transaction ID & Internal Reference
                TextColumn::make('transaction_id')
                    ->label('Transaction Ref')
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Transaction ID copied')
                    ->searchable()
                    ->placeholder('N/A (Manual / Local)')
                    ->description(fn ($record) => "Internal ID: #{$record->id}"),

                // 2. Styled Method Badge with Icons & Colors
                TextColumn::make('method')
                    ->label('Payment Method')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'cod', 'cash on delivery' => 'Cash on Delivery',
                        'card' => 'Credit Card',
                        default => strtoupper($state ?? 'N/A'),
                    })
                    ->color(fn (?string $state): string => match (strtolower($state ?? '')) {

                        'cod', 'cash on delivery' => 'warning',
                        'card' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (?string $state): string => match (strtolower($state ?? '')) {
                        'card' => 'heroicon-m-credit-card',
                        'cod', 'cash on delivery' => 'heroicon-m-banknotes',
                        default => 'heroicon-m-receipt-percent',
                    }),

                // 3. Amount Paid
                TextColumn::make('amount')
                    ->label('Amount Paid')
                    ->money('USD')
                    ->weight('bold')
                    ->sortable(),

                // 4. Payment Status Enum Badge
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                // 5. Date & Time
                TextColumn::make('created_at')
                    ->label('Date Paid')
                    ->dateTime('M d, Y H:i')
                    ->description(fn ($record) => $record->created_at?->diffForHumans())
                    ->sortable()
                    ->color('gray')
                    ->size('sm'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
