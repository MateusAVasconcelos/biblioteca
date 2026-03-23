<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum BookGenre: string implements HasLabel
{
    case Fiction = 'fiction';
    case NonFiction = 'non_fiction';
    case Science = 'science';
    case Technology = 'technology';
    case History = 'history';
    case Biography = 'biography';
    case Philosophy = 'philosophy';
    case Art = 'art';
    case Poetry = 'poetry';
    case Comics = 'comics';
    case Children = 'children';
    case Romance = 'romance';
    case Mystery = 'mystery';
    case Horror = 'horror';
    case Fantasy = 'fantasy';
    case ScienceFiction = 'science_fiction';

    public function getLabel(): string
    {
        return match ($this) {
            self::Fiction => 'Fiction',
            self::NonFiction => 'Non-Fiction',
            self::Science => 'Science',
            self::Technology => 'Technology',
            self::History => 'History',
            self::Biography => 'Biography',
            self::Philosophy => 'Philosophy',
            self::Art => 'Art',
            self::Poetry => 'Poetry',
            self::Comics => 'Comics',
            self::Children => 'Children',
            self::Romance => 'Romance',
            self::Mystery => 'Mystery',
            self::Horror => 'Horror',
            self::Fantasy => 'Fantasy',
            self::ScienceFiction => 'Science Fiction',
        };
    }

    /** @return array<string, string> */
    public static function toOptions(): array
    {
        return array_column(
            array_map(fn (self $genre) => ['value' => $genre->value, 'label' => $genre->getLabel()], self::cases()),
            'label',
            'value',
        );
    }
}
