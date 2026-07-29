<?php

namespace App\Filament\Admin\Resources\Categories\Tables;

use App\Models\Category;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->formatStateUsing(fn ($state, Category $record) => str_repeat(' ↳ ', match ($record->depth) {
                        'gender' => 0,
                        'category' => 1,
                        'subcategory' => 2,
                        default => 0,
                    }).$state
                    )
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent')
                    ->label('Top Level')
                    ->state(function (Category $record): string {
                        return $record->parent?->parent?->name
                            ? $record->parent?->parent?->name.' - '.$record->parent?->name
                            : $record->parent?->name
                            ?? 'Top Level';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        str_starts_with($state, 'Men') => 'info',
                        str_starts_with($state, 'Women') => 'danger',
                        default => 'primary',
                    })->searchable(query: function (Builder $query, string $search): Builder {
                        $isMenSearch = strcasecmp(trim($search), 'men') === 0;
                        // $isMenSearch = strcasecmp(trim($search), 'Men') === 0;

                        return $query->where(function ($mainQuery) use ($search, $isMenSearch) {
                            $mainQuery->whereHas('parent', function ($q) use ($search, $isMenSearch) {
                                if ($isMenSearch) {
                                    $q->where('name', '=', 'Men'); // Lock immediate parent to Men
                                } else {
                                    $q->where('name', 'like', "%{$search}%");
                                }

                                $q->orWhereHas('parent', function ($q2) use ($search, $isMenSearch) {
                                    if ($isMenSearch) {
                                        $q2->where('name', '=', 'Men'); // Lock grandparent to Men
                                    } else {
                                        $q2->where('name', 'like', "%{$search}%");
                                    }
                                });
                            });
                        });
                    }),

                // TextColumn::make('parent.name')
                //     ->searchable(),
                // TextColumn::make('parent.parent.name')
                //     ->searchable(),

                TextColumn::make('depth')
                    ->badge()
                    ->colors([
                        'gray' => 'gender',
                        'primary' => 'category',
                        'success' => 'subcategory',
                    ]),

                TextColumn::make('slug')
                    ->fontFamily('mono'),

                TextColumn::make('children_count')
                    ->counts('children')
                    ->label('Sub-levels'),

                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
                DeleteAction::make()
                    ->before(function (Category $record, DeleteAction $action) {
                        // Secure transactional check: block deleting if dependencies exist
                        if ($record->children()->exists() || $record->products()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Cannot delete Category')
                                ->body('This category has active products or sub-categories attached to it.')
                                ->send();

                            $action->cancel();
                        }
                    }),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
