<?php

namespace App\Filament\Admin\Resources\Fits\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class FitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->placeholder('e.g., Oversized, Slim Fit, Regular')
                    ->maxLength(255),
            ]);
    }
}
