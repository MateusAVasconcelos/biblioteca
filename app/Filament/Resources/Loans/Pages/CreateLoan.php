<?php

namespace App\Filament\Resources\Loans\Pages;

use App\Filament\Resources\Loans\LoanResource;
use App\Http\Controllers\LoanController;
use App\Models\Loan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $loan = Loan::create($data);

        app(LoanController::class)->applyBookEffect($loan->book, $loan->status);

        return $loan;
    }
}
