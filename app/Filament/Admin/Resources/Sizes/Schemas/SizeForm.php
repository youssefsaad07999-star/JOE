<?php

namespace App\Filament\Admin\Resources\Sizes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SizeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->placeholder('e.g., Medium, XL, 32×34')
                    ->maxLength(255),

                Select::make('type')
                    ->options(['alpha' => 'Alpha', 'numeric' => 'Numeric'])
                    ->required(),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first in lists (e.g., S=1, M=2, L=3).'),
            ]);
    }
}
