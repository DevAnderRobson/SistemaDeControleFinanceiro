<?php

namespace App\Enums;

enum EntryType: string
{
    case Payable = 'payable';
    case Receivable = 'receivable';

    public function label(): string
    {
        return match ($this) {
            self::Payable => 'A Pagar',
            self::Receivable => 'A Receber',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Payable => 'bg-red-50 text-red-700 border-red-200',
            self::Receivable => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
