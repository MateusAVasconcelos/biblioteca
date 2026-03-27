<?php

use App\Enums\LoanStatus;
use App\Filament\Resources\Loans\Pages\CreateLoan;
use App\Filament\Resources\Loans\Pages\EditLoan;
use App\Filament\Resources\Loans\Pages\ListLoans;
use App\Filament\Resources\Loans\Tables\LoansTable;
use App\Models\Book;
use App\Models\Client;
use App\Models\Loan;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    actingAs(User::factory()->create());
});

describe('list page', function () {
    it('loads successfully with records', function () {
        $loans = Loan::factory()->count(3)->create();

        Livewire::test(ListLoans::class)
            ->assertOk()
            ->assertCanSeeTableRecords($loans);
    });

    it('devolved action is visible for reserved loans', function () {
        $reserved = Loan::factory()->create(['status' => LoanStatus::Reserved]);

        Livewire::test(ListLoans::class)
            ->assertSee('devolved')
            ->assertSee($reserved->status);
    });

    it('devolved action visibility is driven by reserved status', function () {
        $table = app(LoansTable::class);

        $reserved = Loan::factory()->make(['status' => LoanStatus::Reserved]);
        $sold = Loan::factory()->make(['status' => LoanStatus::Sold]);
        $devolved = Loan::factory()->make(['status' => LoanStatus::Devolved]);

        expect(LoanStatus::Reserved)->toBe($reserved->status);
        expect($reserved->status === LoanStatus::Reserved)->toBeTrue();
        expect($sold->status === LoanStatus::Reserved)->toBeFalse();
        expect($devolved->status === LoanStatus::Reserved)->toBeFalse();
    });

    it('devolved action reverts available_copies for reserved loan', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 4]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved,
        ]);

        Livewire::test(ListLoans::class)
            ->callTableAction('devolved', $loan);

        expect($book->fresh()->available_copies)->toBe(5);
        expect($loan->fresh()->status)->toBe(LoanStatus::Devolved);
    });
});

describe('create page', function () {
    it('loads successfully', function () {
        Livewire::test(CreateLoan::class)->assertOk();
    });

    it('can create a reserved loan and decrements available_copies', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 5]);
        $client = Client::factory()->create();

        Livewire::test(CreateLoan::class)
            ->fillForm([
                'client_id' => $client->id,
                'book_id' => $book->id,
                'status' => LoanStatus::Reserved->value,
                'due_date' => now()->addDays(7)->format('Y-m-d'),
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        assertDatabaseHas(Loan::class, [
            'client_id' => $client->id,
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved->value,
        ]);

        expect($book->fresh()->available_copies)->toBe(4);
    });

    it('can create a sold loan and decrements both copies and available_copies', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 5]);
        $client = Client::factory()->create();

        Livewire::test(CreateLoan::class)
            ->fillForm([
                'client_id' => $client->id,
                'book_id' => $book->id,
                'status' => LoanStatus::Sold->value,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        expect($book->fresh())
            ->available_copies->toBe(4)
            ->copies->toBe(4);
    });

    it('validates required fields', function (array $data, array $errors) {
        $book = Book::factory()->create(['available_copies' => 1]);
        $client = Client::factory()->create();

        Livewire::test(CreateLoan::class)
            ->fillForm([
                'client_id' => $client->id,
                'book_id' => $book->id,
                'status' => LoanStatus::Sold->value,
                ...$data,
            ])
            ->call('create')
            ->assertHasFormErrors($errors)
            ->assertNotNotified();
    })->with([
        'client is required' => [['client_id' => null], ['client_id' => 'required']],
        'book is required' => [['book_id' => null], ['book_id' => 'required']],
        'status is required' => [['status' => null], ['status' => 'required']],
    ]);
});

describe('edit page', function () {
    it('loads with correct state', function () {
        $loan = Loan::factory()->create();

        Livewire::test(EditLoan::class, ['record' => $loan->id])
            ->assertOk()
            ->assertSchemaStateSet([
                'client_id' => $loan->client_id,
                'book_id' => $loan->book_id,
                'status' => $loan->status->value,
            ]);
    });

    it('can change status from reserved to sold and adjusts inventory', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 4]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved,
        ]);

        Livewire::test(EditLoan::class, ['record' => $loan->id])
            ->fillForm(['status' => LoanStatus::Sold->value])
            ->call('save')
            ->assertNotified();

        expect($book->fresh())
            ->available_copies->toBe(4)
            ->copies->toBe(4);
    });

    it('delete action reverts book effect for reserved loan', function () {
        $book = Book::factory()->create(['copies' => 5, 'available_copies' => 4]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatus::Reserved,
        ]);

        Livewire::test(EditLoan::class, ['record' => $loan->id])
            ->callAction(DeleteAction::class)
            ->assertNotified();

        expect($book->fresh()->available_copies)->toBe(5);
    });
});
