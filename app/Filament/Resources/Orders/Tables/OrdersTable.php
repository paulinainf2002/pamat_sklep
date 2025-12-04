<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Numer zamówienia')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Klient')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(),

                TextColumn::make('total')
                    ->label('Suma')
                    ->money('pln')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'failed',
                    ]),

                TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Oczekujące',
                        'paid'    => 'Opłacone',
                        'failed'  => 'Nieudane',
                    ]),
            ])

            ->recordActions([
                EditAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
