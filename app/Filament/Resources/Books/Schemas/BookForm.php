<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Enums\BookGenre;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Book Information')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('author')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('isbn')
                            ->label('ISBN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        TextInput::make('publisher')
                            ->maxLength(255),
                        Select::make('genre')
                            ->options(BookGenre::toOptions())
                            ->searchable(),
                        DatePicker::make('published_at')
                            ->label('Published At')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Stock')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('copies')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(1),
                    ]),

                Section::make('Pricing')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('loan_price')
                            ->label('Loan Price')
                            ->numeric()
                            ->prefix('R$')
                            ->minValue(0),
                        TextInput::make('sale_price')
                            ->label('Sale Price')
                            ->numeric()
                            ->prefix('R$')
                            ->minValue(0),
                    ]),
            ]);
    }
}
