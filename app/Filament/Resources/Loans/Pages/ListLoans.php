<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Enums\LoanStatus;
use App\Filament\Resources\Loans\LoanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLoans extends ListRecords
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'reserved' => Tab::make('Reserved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LoanStatus::Reserved->value)),
            'sold' => Tab::make('Sold')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LoanStatus::Sold->value)),
            'devolved' => Tab::make('Devolved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', LoanStatus::Devolved->value)),
            'all' => Tab::make('All'),
        ];
    }
}
