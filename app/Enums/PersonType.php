<?php

namespace App\Enums;

enum PersonType: string
{
    case Customer = 'customer';
    case Supplier = 'supplier';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Cliente',
            self::Supplier => 'Fornecedor',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Customer => 'bg-sky-50 text-sky-700 border-sky-200',
            self::Supplier => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        };
    }
}
