<?php

use App\Enums\LoanStatus;

it('has the correct values', function () {
    expect(LoanStatus::Reserved->value)->toBe('reserved');
    expect(LoanStatus::Sold->value)->toBe('sold');
    expect(LoanStatus::Devolved->value)->toBe('devolved');
});

it('returns correct labels', function () {
    expect(LoanStatus::Reserved->getLabel())->toBe('Reserved');
    expect(LoanStatus::Sold->getLabel())->toBe('Sold');
    expect(LoanStatus::Devolved->getLabel())->toBe('Devolved');
});

it('returns correct colors', function () {
    expect(LoanStatus::Reserved->getColor())->toBe('warning');
    expect(LoanStatus::Sold->getColor())->toBe('success');
    expect(LoanStatus::Devolved->getColor())->toBe('info');
});

it('can be resolved from string value', function () {
    expect(LoanStatus::from('reserved'))->toBe(LoanStatus::Reserved);
    expect(LoanStatus::from('sold'))->toBe(LoanStatus::Sold);
    expect(LoanStatus::from('devolved'))->toBe(LoanStatus::Devolved);
});
