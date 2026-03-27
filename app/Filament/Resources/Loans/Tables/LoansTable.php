<?php

namespace App\Filament\Resources\Loans\Tables;

use App\Enums\LoanStatus;
use App\Filament\Resources\Clients\ClientResource;
use App\Http\Controllers\LoanController;
use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->url(fn (Loan $record): string => ClientResource::getUrl('edit', ['record' => $record]))
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (TextColumn $column): ?string => strlen((string) $column->getState()) > 25 ? $column->getState() : null),
                TextColumn::make('book.title')
                    ->label('Book')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (TextColumn $column): ?string => strlen((string) $column->getState()) > 35 ? $column->getState() : null),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->poll('10s')
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->recordActions([
                Action::make('devolved')
                    ->label('Devolved')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Loan $record) => $record->status === LoanStatus::Reserved)
                    ->action(fn (Loan $record) => app(LoanController::class)->revertBookEffect($record->book, $record->status, $record)),
            ]);
    }
}
