<?php

namespace App\Filament\Resources\Loans\Schemas;

use App\Enums\LoanStatus;
use App\Models\Book;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('client_id')
                    ->label('Client')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('book_id')
                    ->label('Book')
                    ->options(
                        Book::query()
                            ->where('available_copies', '>', 0)
                            ->pluck('title', 'id')
                    )
                    ->searchable()
                    ->required(),

                Select::make('status')
                    ->options(collect(LoanStatus::cases())
                        ->reject(fn (LoanStatus $status) => $status === LoanStatus::Devolved)
                        ->mapWithKeys(fn (LoanStatus $status) => [$status->value => $status->getLabel()])
                    )
                    ->live()
                    ->required(),

                DatePicker::make('due_date')
                    ->label('Due Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->minDate(today())
                    ->visible(fn (callable $get) => $get('status') === LoanStatus::Reserved->value)
                    ->required(fn (callable $get) => $get('status') === LoanStatus::Reserved->value),
            ]);
    }
}
