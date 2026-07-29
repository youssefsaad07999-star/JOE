<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Account Details')
                    ->columnSpanFull()
                    ->schema([
                        // 1. Roles (Using Spatie HasRoles relationship)
                        TextEntry::make('roles.name')
                            ->label('Role')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => str($state)->headline())
                            ->color(fn (string $state): string => match (strtolower($state)) {
                                'super_admin', 'admin' => 'danger',
                                'manager' => 'warning',
                                default => 'info',
                            })
                            ->placeholder('Customer'),

                        // 2. Combined Name
                        TextEntry::make('full_name')
                            ->label('Full Name')
                            ->state(fn ($record) => trim("{$record->first_name} {$record->last_name}"))
                            ->weight('bold')
                            ->icon('heroicon-m-user'),

                        // 3. Email (Copyable)
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->copyMessage('Email copied!'),

                        // 4. Phone Number
                        TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->icon('heroicon-m-phone')
                            ->placeholder('Not provided')
                            ->copyable(),

                        // 5. Date of Birth
                        TextEntry::make('date_of_birth')
                            ->label('Date of Birth')
                            ->date('F j, Y')
                            ->icon('heroicon-m-calendar')
                            ->placeholder('Not provided'),

                        // 6. Verification Badge instead of plain timestamp
                        TextEntry::make('email_verified_at')
                            ->label('Verification Status')
                            ->badge()
                            ->state(fn ($record) => $record->email_verified_at ? 'Verified' : 'Unverified')
                            ->color(fn (string $state): string => $state === 'Verified' ? 'success' : 'warning')
                            ->icon(fn (string $state): string => $state === 'Verified' ? 'heroicon-m-check-badge' : 'heroicon-m-x-circle')
                            ->helperText(fn ($record) => $record->email_verified_at?->format('M d, Y H:i')),

                        // 7. Creation & Activity Timestamps
                        TextEntry::make('created_at')
                            ->label('Joined Date')
                            ->dateTime('M d, Y H:i')
                            ->helperText(fn ($record) => $record->created_at?->diffForHumans()),

                        TextEntry::make('updated_at')
                            ->label('Last Profile Update')
                            ->dateTime('M d, Y H:i')
                            ->helperText(fn ($record) => $record->updated_at?->diffForHumans())
                            ->color('gray'),
                    ])
                    ->columns(2),
            ]);
    }
}
