<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use App\Http\Controllers\LoanController;
use App\Models\Loan;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditLoan extends EditRecord
{
    protected static string $resource = LoanResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $originalStatus = $record->status;

        $record->update($data);

        if ($record->wasChanged('status')) {
            $controller = app(LoanController::class);
            $controller->revertBookEffect($record->book, $originalStatus);
            $controller->applyBookEffect($record->book, $record->status);
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(fn (Loan $record) => app(LoanController::class)->revertBookEffect($record->book, $record->status)),
            ForceDeleteAction::make(),
            RestoreAction::make()
                ->after(fn (Loan $record) => app(LoanController::class)->applyBookEffect($record->book, $record->status)),
        ];
    }
}
