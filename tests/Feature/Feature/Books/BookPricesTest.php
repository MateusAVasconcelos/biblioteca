<?php

use App\Models\Book;

it('stores loan_price and sale_price on a book', function () {
    $book = Book::factory()->create([
        'loan_price' => 5.99,
        'sale_price' => 49.90,
    ]);

    expect($book->fresh())
        ->loan_price->toEqual('5.99')
        ->sale_price->toEqual('49.90');
});

it('allows books without prices', function () {
    $book = Book::factory()->create([
        'loan_price' => null,
        'sale_price' => null,
    ]);

    expect($book->fresh())
        ->loan_price->toBeNull()
        ->sale_price->toBeNull();
});
