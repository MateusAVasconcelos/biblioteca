<?php

namespace App\Http\Controllers;

use App\Enums\LoanStatus;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;

class LoanController extends Controller
{
    public function store(StoreLoanRequest $request): RedirectResponse
    {
        $loan = Loan::create($request->validated());

        $this->applyBookEffect($loan->book, $loan->status);

        return redirect()->back();
    }

    public function update(UpdateLoanRequest $request, Loan $loan): RedirectResponse
    {
        $originalStatus = $loan->status;

        $loan->update($request->validated());

        if ($loan->wasChanged('status')) {
            $this->revertBookEffect($loan->book, $originalStatus);
            $this->applyBookEffect($loan->book, $loan->status);
        }

        return redirect()->back();
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->revertBookEffect($loan->book, $loan->status, $loan);

        return redirect()->back();
    }

    public function applyBookEffect(Book $book, LoanStatus $status): void
    {
        match ($status) {
            LoanStatus::Reserved => $book->decrement('available_copies'),
            LoanStatus::Sold => tap($book)->decrement('available_copies')->decrement('copies'),
            LoanStatus::Devolved => null,
        };
    }

    public function revertBookEffect(Book $book, LoanStatus $status, ?Loan $loan = null): void
    {
        match ($status) {
            LoanStatus::Reserved => $book->increment('available_copies'),
            LoanStatus::Sold => tap($book)->increment('available_copies')->increment('copies'),
            LoanStatus::Devolved => null,
        };

        $loan?->update(['status' => LoanStatus::Devolved]);
    }
}
