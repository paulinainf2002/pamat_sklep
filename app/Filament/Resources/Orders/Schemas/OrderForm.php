<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([

            // --- DANE ZAMÓWIENIA ---
            TextInput::make('order_number')
                ->label('Numer zamówienia')
                ->disabled()
                ->columnSpan(2),

            TextInput::make('total')
                ->label('Suma (PLN)')
                ->disabled(),

            Select::make('status')
                ->label('Status')
                ->options([
                    'pending' => 'Oczekujące',
                    'paid'    => 'Opłacone',
                    'failed'  => 'Nieudane',
                ])
                ->required(),

            // --- DANE KLIENTA ---
            TextInput::make('name')
                ->label('Imię i nazwisko')
                ->required()
                ->columnSpanFull(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),

            TextInput::make('phone')
                ->label('Telefon')
                ->required(),

            TextInput::make('address')
                ->label('Adres')
                ->required(),

            TextInput::make('postal_code')
                ->label('Kod pocztowy')
                ->required(),

            TextInput::make('city')
                ->label('Miasto')
                ->required(),

            // --- POZYCJE ZAMÓWIENIA (TYLKO PODGLĄD) ---
            Repeater::make('items')
                ->label('Produkty w zamówieniu')
                ->schema([
                    TextInput::make('product_id')
                        ->label('ID produktu')
                        ->disabled(),

                    TextInput::make('quantity')
                        ->label('Ilość')
                        ->disabled(),

                    TextInput::make('price')
                        ->label('Cena (PLN)')
                        ->disabled(),
                ])
                ->disabled()
                ->columnSpanFull(),
        ]);
    }
}
