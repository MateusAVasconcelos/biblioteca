<?php

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Client;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

beforeEach(function () {
    actingAs(User::factory()->create());
});

describe('store', function () {
    it('creates a reserved loan and decrements available_copies', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 5]);

        post(route('loans.store'), [
            'client_id' => Client::factory()->create()->id,
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved->value,
        ])->assertRedirect();

        expect($book->fresh()->available_copies)->toBe(4);
    });

    it('creates a sold loan and decrements both copies and available_copies', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 5]);

        post(route('loans.store'), [
            'client_id' => Client::factory()->create()->id,
            'book_id' => $book->id,
            'status' => LoanStatus::Sold->value,
        ])->assertRedirect();

        expect($book->fresh())
            ->available_copies->toBe(4)
            ->copies->toBe(4);
    });
});

describe('destroy', function () {
    it('sets loan status to devolved when destroying a reserved loan', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 4]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved,
        ]);

        delete(route('loans.destroy', $loan))->assertRedirect();

        expect($loan->fresh()->status)->toBe(LoanStatus::Devolved);
    });

    it('increments available_copies when destroying a reserved loan', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 4]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved,
        ]);

        delete(route('loans.destroy', $loan))->assertRedirect();

        expect($book->fresh()->available_copies)->toBe(5);
    });

    it('does not delete the loan when destroying', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 4]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved,
        ]);

        delete(route('loans.destroy', $loan))->assertRedirect();

        expect(Loan::find($loan->id))->not->toBeNull();
    });

    it('sets loan status to devolved when destroying a sold loan', function () {
        $book = Book::factory()->create(['copies' => 4, 'available_copies' => 3]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Sold,
        ]);

        delete(route('loans.destroy', $loan))->assertRedirect();

        expect($loan->fresh()->status)->toBe(LoanStatus::Devolved);
    });

    it('increments both copies and available_copies when destroying a sold loan', function () {
        $book = Book::factory()->create(['copies' => 4, 'available_copies' => 3]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Sold,
        ]);

        delete(route('loans.destroy', $loan))->assertRedirect();

        expect($book->fresh())
            ->available_copies->toBe(4)
            ->copies->toBe(5);
    });
});

describe('update', function () {
    it('reverts and applies book effect when status changes from reserved to sold', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 4]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved,
        ]);

        put(route('loans.update', $loan), [
            'client_id' => $loan->client_id,
            'book_id' => $book->id,
            'status' => LoanStatus::Sold->value,
        ])->assertRedirect();

        // Reserved reverted (+1 available), Sold applied (-1 available, -1 copies)
        expect($book->fresh())
            ->available_copies->toBe(4)
            ->copies->toBe(4);
    });
});
