<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // ── Main Content Area (2/3 Width) ─────────────────────────────
                Group::make([

                    // ── Product Identity ──────────────────────────────────
                    Section::make('Product Overview')
                        ->schema([
                            // Top Row: Core Details
                            Grid::make(3)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Product Name')
                                        ->size('large')
                                        ->weight(FontWeight::Bold),

                                    TextEntry::make('base_price')
                                        ->label('Base Price')
                                        ->money('USD')
                                        ->color('success')
                                        ->size('large')
                                        ->weight(FontWeight::SemiBold),

                                    TextEntry::make('category.parent.parent.name')
                                        ->label('Gender')
                                        ->badge()
                                        ->color('gray')
                                        ->placeholder('N/A'),
                                ]),

                            // Bottom Row: Image Gallery
                            Section::make('Product Gallery')
                                ->collapsible()
                                ->schema([
                                    RepeatableEntry::make('images')
                                        ->label('')
                                        ->hiddenLabel()
                                        ->grid(3) // Keeps images in a nice horizontal row
                                        ->schema([
                                            ImageEntry::make('image_path')
                                                ->hiddenLabel()
                                                ->disk('public')
                                                ->square()
                                                ->imageSize(120)
                                                ->extraImgAttributes(['class' => 'rounded-xl object-cover shadow-md']),
                                            TextEntry::make('color.name')
                                                ->hiddenLabel()
                                                ->alignCenter()
                                                ->badge()
                                                ->color('gray')
                                                ->formatStateUsing(fn ($state) => ucfirst($state))
                                                ->placeholder('Global Asset'),
                                        ]),
                                ]),
                        ]),

                    // ── Classification ────────────────────────────────────
                    Section::make('Classification')
                        ->icon('heroicon-m-squares-2x2')
                        ->columns(3)
                        ->schema([
                            TextEntry::make('category.parent.name')
                                ->label('Category')
                                ->icon('heroicon-m-folder')
                                ->weight(FontWeight::Medium),

                            TextEntry::make('category.name')
                                ->label('Subcategory')
                                ->icon('heroicon-m-tag')
                                ->weight(FontWeight::Medium)
                                ->default('Unassigned'),

                            TextEntry::make('brand.name')
                                ->label('Brand')
                                ->icon('heroicon-m-building-office')
                                ->weight(FontWeight::Medium)
                                ->default('—'),

                            TextEntry::make('fit.name')
                                ->label('Sizing / Fit')
                                ->icon('heroicon-m-user')
                                ->formatStateUsing(fn ($state): string => ucfirst($state))
                                ->weight(FontWeight::Medium)
                                ->default('Standard Fit'),
                        ]),

                    // ── Description ───────────────────────────────────────
                    Section::make('Description')
                        ->icon('heroicon-m-document-text')
                        ->collapsible()
                        ->schema([
                            TextEntry::make('description')
                                ->hiddenLabel()
                                ->markdown()
                                ->prose()
                                ->placeholder('No description provided.'),
                        ]),

                ])
                    ->columnSpan(['lg' => 2]), // Closes the 2/3 Main Column Group

                // ── Sidebar Context (1/3 Width) ───────────────────────────────
                Group::make([

                    Section::make('Status')
                        ->icon('heroicon-m-signal')
                        ->schema([
                            IconEntry::make('is_active')
                                ->label('Storefront Visibility')
                                ->boolean()
                                ->inlineLabel()
                                ->trueIcon('heroicon-m-eye')
                                ->falseIcon('heroicon-m-eye-slash')
                                ->trueColor('success')
                                ->falseColor('danger'),

                            TextEntry::make('variants_count')
                                ->label('Total Variants')
                                ->state(fn (Product $record) => $record->variants->count())
                                ->icon('heroicon-m-square-3-stack-3d'),

                            TextEntry::make('total_stock')
                                ->label('Total Stock')
                                ->state(fn (Product $record) => $record->variants->sum('stock_quantity'))
                                ->icon('heroicon-m-archive-box')
                                ->color(fn ($state) => match (true) {
                                    $state === 0 => 'danger',
                                    $state <= 10 => 'warning',
                                    default => 'success',
                                }),
                        ]),

                    Section::make('Audit')
                        ->icon('heroicon-m-clock')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Created')
                                ->dateTime('M d, Y @ H:i')
                                ->icon('heroicon-m-calendar')
                                ->color('gray'),

                            TextEntry::make('updated_at')
                                ->label('Last Updated')
                                ->since()
                                ->icon('heroicon-m-arrow-path')
                                ->color('gray'),
                        ]),

                ])
                    ->columnSpan(['lg' => 1]), // Closes the 1/3 Sidebar Group

                // ── Breakaway Footer Row (Full 3/3 Width) ─────────────────────
                Section::make('Variants')
                    ->collapsible()
                    ->icon('heroicon-m-swatch')
                    ->columnSpanFull() // Extends edge-to-edge across the dashboard floor
                    ->schema([
                        RepeatableEntry::make('variants')
                            ->hiddenLabel()
                            ->columns(5)
                            ->schema([
                                TextEntry::make('color.name')
                                    ->label('Colour')
                                    ->formatStateUsing(fn ($state, $record) => '<span style="display:inline-flex;align-items:center;gap:6px;">'
                                         .'<span style="width:10px;height:10px;border-radius:50%;'
                                         .'background:'.($record->color->hex_code ?? '#ccc').';'
                                         .'flex-shrink:0;"></span>'
                                           .htmlspecialchars($state ?? '—')
                                         .'</span>'
                                    )
                                    ->html(),

                                TextEntry::make('size.name')
                                    ->label('Size')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('sku')
                                    ->label('SKU')
                                    ->fontFamily(FontFamily::Mono)
                                    ->copyable()
                                    ->copyMessage('Copied!'),

                                TextEntry::make('stock_quantity')
                                    ->label('Stock')
                                    ->badge()
                                    ->color(fn ($state) => match (true) {
                                        $state === 0 => 'danger',
                                        $state <= 5 => 'warning',
                                        default => 'success',
                                    }),

                                TextEntry::make('price_override')
                                    ->label('Price Override')
                                    ->money('USD')
                                    ->default('Base price')
                                    ->color(fn ($state) => $state ? 'primary' : 'gray'),
                            ]),
                    ]),

            ])
            ->columns(['lg' => 3]); // Master grid configuration layout control
    }
}
