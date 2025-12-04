<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([

            // DANE KLIENTA
            TextInput::make('order_number')
                ->label('Numer zamówienia')
                ->disabled(),

            TextInput::make('name')
                ->label('Imię i nazwisko')
                ->disabled(),

            TextInput::make('email')
                ->label('Adres e-mail')
                ->disabled(),

            TextInput::make('phone')
                ->label('Telefon')
                ->disabled(),

            // DOSTAWA
            TextInput::make('delivery_method')
                ->label('Metoda dostawy')
                ->disabled(),

            TextInput::make('delivery_point')
                ->label('Paczkomat InPost')
                ->disabled(),

            TextInput::make('address')
                ->label('Adres (kurier)')
                ->disabled(),

            TextInput::make('city')
                ->label('Miasto')
                ->disabled(),

            TextInput::make('postal_code')
                ->label('Kod pocztowy')
                ->disabled(),

            // PŁATNOŚĆ
            TextInput::make('payment_method')
                ->label('Metoda płatności')
                ->disabled(),

            TextInput::make('payment_status')
                ->label('Status płatności')
                ->disabled(),

            // PODSUMOWANIE
            TextInput::make('total')
                ->label('Kwota zamówienia')
                ->prefix('PLN')
                ->disabled(),

            TextInput::make('status')
                ->label('Status zamówienia'),
        ]);
    }
}
