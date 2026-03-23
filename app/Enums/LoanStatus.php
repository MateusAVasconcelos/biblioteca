<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LoanStatus: string implements HasColor, HasLabel
{
    case Reserved = 'reserved';
    case Sold = 'sold';
    case Devolved = 'devolved';

    public function getLabel(): string
    {
        return match ($this) {
            self::Reserved => 'Reserved',
            self::Sold => 'Sold',
            self::Devolved => 'Devolved',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Reserved => 'warning',
            self::Sold => 'success',
            self::Devolved => 'info',
        };
    }
}
