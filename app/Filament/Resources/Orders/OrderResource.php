<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Models\Order;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    /**
     * Ikona w menu nawigacji (typ zgodny z bazową klasą Resource).
     */
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedClipboardDocument;

    /**
     * Grupa w menu nawigacji – u Ciebie bazowy Resource wymaga typu
     * UnitEnum|string|null, więc używamy zwykłego stringa.
     */
    protected static UnitEnum|string|null $navigationGroup = 'Sklep';

    /**
     * Kolumna używana jako tytuł rekordu.
     */
    protected static ?string $recordTitleAttribute = 'order_number';

    /**
     * Formularz (create / edit) – delegujemy do OrderForm::configure().
     */
    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    /**
     * Tabela listy zamówień – delegujemy do OrdersTable::configure().
     */
    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    /**
     * Strony resource’a.
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
