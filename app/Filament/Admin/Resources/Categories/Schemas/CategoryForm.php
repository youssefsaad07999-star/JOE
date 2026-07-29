<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('parent_id')
                    ->label('Parent Category (leave blank for top Gender level)')
                    ->options(fn () => Category::whereIn('depth', ['gender', 'category'])
                        ->get()
                        ->mapWithKeys(fn ($c) => [$c->id => "[{$c->parent?->name}] {$c->name}"])
                    )
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (! $state) {
                            $set('depth', 'gender');

                            return;
                        }
                        $parent = Category::find($state);
                        $set('depth', match ($parent?->depth) {
                            'gender' => 'category',
                            'category' => 'subcategory',
                            default => 'gender',
                        });
                    })
                    ->nullable(),

                TextInput::make('name')
                    ->required()
                    ->live(debounce: 400)
                    ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))
                    ),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Hidden::make('depth')
                    ->default('gender'),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
