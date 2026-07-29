<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('roles')
                    ->relationship('roles', 'name') // Connects securely to Spatie's roles table
                    ->multiple()                    // Allows multiple roles if necessary
                    ->preload()                     // Boosts UI performance by pre-loading options
                    ->searchable(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DatePicker::make('date_of_birth')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                // TextInput::make('password')
                //     ->password()
                //     ->required(),
            ]);
    }
}
