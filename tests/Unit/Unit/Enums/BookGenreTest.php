<?php

use App\Enums\BookGenre;

it('has 16 genres', function () {
    expect(BookGenre::cases())->toHaveCount(16);
});

it('returns correct labels', function (BookGenre $genre, string $label) {
    expect($genre->getLabel())->toBe($label);
})->with([
    [BookGenre::Fiction, 'Fiction'],
    [BookGenre::NonFiction, 'Non-Fiction'],
    [BookGenre::Science, 'Science'],
    [BookGenre::Technology, 'Technology'],
    [BookGenre::History, 'History'],
    [BookGenre::Biography, 'Biography'],
    [BookGenre::Philosophy, 'Philosophy'],
    [BookGenre::Art, 'Art'],
    [BookGenre::Poetry, 'Poetry'],
    [BookGenre::Comics, 'Comics'],
    [BookGenre::Children, 'Children'],
    [BookGenre::Romance, 'Romance'],
    [BookGenre::Mystery, 'Mystery'],
    [BookGenre::Horror, 'Horror'],
    [BookGenre::Fantasy, 'Fantasy'],
    [BookGenre::ScienceFiction, 'Science Fiction'],
]);

it('toOptions returns an array keyed by value with label as value', function () {
    $options = BookGenre::toOptions();

    expect($options)->toBeArray();
    expect($options)->toHaveCount(16);
    expect($options['fiction'])->toBe('Fiction');
    expect($options['non_fiction'])->toBe('Non-Fiction');
    expect($options['science_fiction'])->toBe('Science Fiction');
});
