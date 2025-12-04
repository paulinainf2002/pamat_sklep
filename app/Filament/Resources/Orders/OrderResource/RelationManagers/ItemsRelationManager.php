<?php

namespace App\Filament\Resources\Orders\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Zamówione produkty';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produkt'),

                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Zdjęcie')
                    ->disk('s3')
                    ->height(50)
                    ->circular(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Ilość'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Cena')
                    ->money('PLN'),
            ])
            ->headerActions([])  // blokada dodawania
            ->actions([])        // blokada akcji
            ->bulkActions([]);   // blokada batch
    }
}
