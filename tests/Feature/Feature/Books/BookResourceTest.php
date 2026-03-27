<?php

use App\Filament\Resources\Books\Pages\CreateBook;
use App\Filament\Resources\Books\Pages\EditBook;
use App\Filament\Resources\Books\Pages\ListBooks;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    actingAs(User::factory()->create());
});

describe('list page', function () {
    it('loads successfully with records', function () {
        $books = Book::factory()->count(3)->create();

        Livewire::test(ListBooks::class)
            ->assertOk()
            ->assertCanSeeTableRecords($books);
    });

    it('can search books by title', function () {
        $books = Book::factory()->count(3)->create();
        $target = $books->first();

        Livewire::test(ListBooks::class)
            ->searchTable($target->title)
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords($books->skip(1));
    });

    it('can search books by author', function () {
        $books = Book::factory()->count(3)->create();
        $target = $books->first();

        Livewire::test(ListBooks::class)
            ->searchTable($target->author)
            ->assertCanSeeTableRecords([$target]);
    });
});

describe('create page', function () {
    it('loads successfully', function () {
        Livewire::test(CreateBook::class)->assertOk();
    });

    it('can create a book', function () {
        $newBook = Book::factory()->make();

        Livewire::test(CreateBook::class)
            ->fillForm([
                'title' => $newBook->title,
                'author' => $newBook->author,
                'isbn' => $newBook->isbn,
                'copies' => $newBook->copies,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        assertDatabaseHas(Book::class, [
            'title' => $newBook->title,
            'isbn' => $newBook->isbn,
        ]);
    });

    it('can create a book with prices', function () {
        $newBook = Book::factory()->make();

        Livewire::test(CreateBook::class)
            ->fillForm([
                'title' => $newBook->title,
                'author' => $newBook->author,
                'isbn' => $newBook->isbn,
                'copies' => $newBook->copies,
                'loan_price' => 9.99,
                'sale_price' => 59.90,
            ])
            ->call('create')
            ->assertNotified()
            ->assertRedirect();

        assertDatabaseHas(Book::class, [
            'isbn' => $newBook->isbn,
            'loan_price' => 9.99,
            'sale_price' => 59.90,
        ]);
    });

    it('validates required fields', function (array $data, array $errors) {
        $newBook = Book::factory()->make();

        Livewire::test(CreateBook::class)
            ->fillForm([
                'title' => $newBook->title,
                'author' => $newBook->author,
                'isbn' => $newBook->isbn,
                'copies' => $newBook->copies,
                ...$data,
            ])
            ->call('create')
            ->assertHasFormErrors($errors)
            ->assertNotNotified();
    })->with([
        'title is required' => [['title' => null], ['title' => 'required']],
        'title is max 255' => [['title' => Str::random(256)], ['title' => 'max']],
        'author is required' => [['author' => null], ['author' => 'required']],
        'isbn is required' => [['isbn' => null], ['isbn' => 'required']],
        'copies is required' => [['copies' => null], ['copies' => 'required']],
    ]);

    it('validates isbn uniqueness', function () {
        $existing = Book::factory()->create();

        Livewire::test(CreateBook::class)
            ->fillForm([
                'title' => 'New Title',
                'author' => 'New Author',
                'isbn' => $existing->isbn,
                'copies' => 1,
            ])
            ->call('create')
            ->assertHasFormErrors(['isbn' => 'unique'])
            ->assertNotNotified();
    });
});

describe('edit page', function () {
    it('loads with correct state', function () {
        $book = Book::factory()->create();

        Livewire::test(EditBook::class, ['record' => $book->id])
            ->assertOk()
            ->assertSchemaStateSet([
                'title' => $book->title,
                'author' => $book->author,
                'isbn' => $book->isbn,
                'copies' => $book->copies,
                'loan_price' => $book->loan_price,
                'sale_price' => $book->sale_price,
            ]);
    });

    it('can update a book', function () {
        $book = Book::factory()->create();
        $newData = Book::factory()->make();

        Livewire::test(EditBook::class, ['record' => $book->id])
            ->fillForm([
                'title' => $newData->title,
                'author' => $newData->author,
                'isbn' => $newData->isbn,
            ])
            ->call('save')
            ->assertNotified();

        assertDatabaseHas(Book::class, [
            'id' => $book->id,
            'title' => $newData->title,
            'isbn' => $newData->isbn,
        ]);
    });

    it('can update prices', function () {
        $book = Book::factory()->create();

        Livewire::test(EditBook::class, ['record' => $book->id])
            ->fillForm([
                'loan_price' => 7.50,
                'sale_price' => 89.90,
            ])
            ->call('save')
            ->assertNotified();

        assertDatabaseHas(Book::class, [
            'id' => $book->id,
            'loan_price' => 7.50,
            'sale_price' => 89.90,
        ]);
    });

    it('allows same isbn when editing own record', function () {
        $book = Book::factory()->create();

        Livewire::test(EditBook::class, ['record' => $book->id])
            ->fillForm(['isbn' => $book->isbn])
            ->call('save')
            ->assertHasNoFormErrors();
    });

    it('validates isbn uniqueness against other records', function () {
        $other = Book::factory()->create();
        $book = Book::factory()->create();

        Livewire::test(EditBook::class, ['record' => $book->id])
            ->fillForm(['isbn' => $other->isbn])
            ->call('save')
            ->assertHasFormErrors(['isbn' => 'unique']);
    });
});
