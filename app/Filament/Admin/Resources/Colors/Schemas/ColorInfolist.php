<?php

namespace App\Filament\Admin\Resources\Colors\Schemas;

use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ColorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextEntry::make('hex_code')
                    ->placeholder('-'),
                ColorEntry::make('hex_code')
                    ->label('Color Preview')
                    ->copyable() // Admins can click the pill to copy the hex code instantly
                    ->copyMessage('Hex code copied to clipboard'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
