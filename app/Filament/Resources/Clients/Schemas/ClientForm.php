<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Enums\BrazilianState;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->mask('(99) 99999-9999')
                            ->maxLength(20),
                        TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(14),
                        DatePicker::make('birth_date')
                            ->label('Birth Date')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ]),

                Section::make('Address')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('address')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('zip_code')
                            ->label('ZIP Code')
                            ->mask('99999-999')
                            ->maxLength(9),
                        TextInput::make('city')
                            ->maxLength(255),
                        Select::make('state')
                            ->options(BrazilianState::toOptions())
                            ->searchable(),
                    ]),
            ]);
    }
}
