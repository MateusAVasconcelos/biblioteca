<?php

namespace App\Filament\Widgets;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Client;
use App\Models\Loan;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Number of Books', Book::query()->count())
                ->icon('heroicon-o-book-open'),
            Stat::make('Copies Available', Book::query()->sum('available_copies'))
                ->icon('heroicon-o-clipboard-document-check'),
            Stat::make('Number of Clients', Client::query()->count())
                ->icon('heroicon-o-users'),
            Stat::make('Number of Reserved Loans', Loan::query()->where('status', LoanStatus::Reserved)->count())
                ->icon('heroicon-o-arrow-path'),
        ];
    }
}