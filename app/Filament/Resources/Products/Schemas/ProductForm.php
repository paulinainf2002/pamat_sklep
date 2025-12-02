<?php

// namespace App\Filament\Resources\Products\Schemas;

// use Filament\Schemas\Schema;

// class ProductForm
// {
//     public static function configure(Schema $schema): Schema
//     {
//         return $schema
//             ->components([
//                 //
//             ]);
//     }
// }


namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->label('Nazwa')
                ->required(),

            Textarea::make('description')
                ->label('Opis')
                ->columnSpanFull(),

            TextInput::make('origin')
                ->label('Pochodzenie'),

            Textarea::make('ingredients')
                ->label('Skład')
                ->columnSpanFull(),

            TextInput::make('price')
                ->label('Cena (PLN)')
                ->numeric()
                ->required(),

            FileUpload::make('image')
                ->disk('s3')
                ->label('Zdjęcie główne')
                ->directory('products')
                ->image()
                ->required(),

            FileUpload::make('image2')
                ->disk('s3')
                ->label('Drugie zdjęcie')
                ->directory('products')
                ->image(),

            Select::make('categories')
                ->label('Kategorie')
                ->relationship('categories', 'name')
                ->multiple()
                ->preload(),
        ]);
    }
}
