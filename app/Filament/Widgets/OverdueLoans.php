<?php

namespace App\Filament\Widgets;

use App\Enums\LoanStatus;
use App\Models\Loan;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class OverdueLoans extends TableWidget
{
    protected static ?string $heading = 'Overdue Loans';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Loan::query()
                    ->with(['client', 'book'])
                    ->where('status', LoanStatus::Reserved)
                    ->whereNotNull('due_date')
                    ->whereDate('due_date', '<', now())
                    ->orderBy('due_date'),
            )
            ->columns([
                TextColumn::make('book.title')
                    ->label('Books')
                    ->searchable(),
                TextColumn::make('client.name')
                    ->label('Clients')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Loan Date')
                    ->date('d/m/Y'),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d/m/Y')
                    ->color('danger'),
            ]);
    }
}
