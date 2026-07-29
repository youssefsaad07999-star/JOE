<?php

namespace App\Filament\Admin\Resources\Products\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Primary Product Thumbnail Anchor
                ImageColumn::make('images.image_path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->imageSize(48)
                    ->limit(1)
                    ->defaultImageUrl(url('/images/placeholder-product.png')),

                // 2. Main Title + Sub-metadata (Brand & Fit consolidated)
                TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => implode(' • ', array_filter([
                        $record->brand?->name,
                        $record->fit?->name ? ucfirst($record->fit->name).' Fit' : null,
                    ])))
                    ->wrap(),

                // 3. Subcategory Badge
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                // 4. Financial Base Price (Right-Aligned for Typography Standards)
                TextColumn::make('base_price')
                    ->label('Price')
                    ->money('EGP')
                    ->sortable()
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->alignEnd(),

                // 5. Aggregated Stock Status (Null-Safe Conditionals)
                TextColumn::make('variants_sum_stock_quantity')
                    ->sum('variants', 'stock_quantity')
                    ->label('Total Stock')
                    ->badge()
                    ->alignCenter()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state ?? 0).' units')
                    ->icon(fn ($state) => match (true) {
                        ($state ?? 0) <= 0 => 'heroicon-m-x-circle',
                        ($state ?? 0) <= 10 => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-check-circle',
                    })
                    ->color(fn ($state) => match (true) {
                        ($state ?? 0) <= 0 => 'danger',
                        ($state ?? 0) <= 10 => 'warning',
                        default => 'success',
                    }),

                // 6. Active Toggle
                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->alignCenter(),

                // 7. Toggleable Secondary Columns
                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Variants')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->fontFamily('mono')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->description(fn ($record) => $record->created_at?->diffForHumans())
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueLabel('Active Products')
                    ->falseLabel('Inactive Products')
                    ->native(false),

                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship(
                        name: 'category',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->with('parent') // 👈 Eager load to prevent N+1 queries
                    )
                    ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->full_path) // 👈 Renders: Men / T-shirt / Basic
                    ->searchable()
                    ->preload()
                    ->native(false),

                SelectFilter::make('brand_id')
                    ->label('Brand')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->actions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('View Details')
                    ->slideOver(),
                EditAction::make()
                    ->iconButton()
                    ->color('primary')
                    ->tooltip('Edit Product'),
                DeleteAction::make()
                    ->iconButton()
                    ->requiresConfirmation()
                    ->tooltip('Delete Product'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
