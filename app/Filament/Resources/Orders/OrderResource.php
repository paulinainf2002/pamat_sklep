<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

// Infolists
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    // Ikona zgodna z Filament 4 (enum)
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedClipboardDocument;

    // Musi być UnitEnum|string|null
    protected static \UnitEnum|string|null $navigationGroup = 'Sklep';

    protected static ?string $recordTitleAttribute = 'order_number';

    /*
    |--------------------------------------------------------------------------
    | FORMULARZ (tworzenie / edycja)
    |--------------------------------------------------------------------------
    */
    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTA ZAMÓWIEŃ
    |--------------------------------------------------------------------------
    */
    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | INFO LIST (panel szczegółów) — tu pokażemy produkty + dane zamówienia
    |--------------------------------------------------------------------------
    */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make('Dane klienta')->schema([
                TextEntry::make('name')->label('Imię i nazwisko'),
                TextEntry::make('email')->label('Email'),
                TextEntry::make('phone')->label('Telefon'),
            ]),

            Section::make('Dostawa')->schema([
                TextEntry::make('delivery_method')
                    ->label('Metoda dostawy')
                    ->formatStateUsing(fn ($state) =>
                        $state === 'inpost' ? 'Paczkomat InPost' : 'Kurier'
                    ),

                TextEntry::make('delivery_point')
                    ->label('Paczkomat'),

                TextEntry::make('address')
                    ->label('Adres'),

                TextEntry::make('postal_code')
                    ->label('Kod pocztowy'),

                TextEntry::make('city')
                    ->label('Miasto'),
            ]),

            Section::make('Płatność')->schema([
                TextEntry::make('payment_method')
                    ->label('Metoda płatności')
                    ->formatStateUsing(fn ($state) =>
                        $state === 'p24' ? 'Przelewy24' : 'Przelew tradycyjny'
                    ),

                TextEntry::make('payment_status')->label('Status płatności'),

                TextEntry::make('total')->label('Kwota')->money('PLN'),
            ]),

            Section::make('Produkty w zamówieniu')->schema([
                ViewEntry::make('items_table')
                    ->view('filament.orders.items-table'),
            ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STRONY
    |--------------------------------------------------------------------------
    */
    public static function getPages(): array
    {
        return [
            'index'  => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit'   => EditOrder::route('/{record}/edit'),
        ];
    }
}
