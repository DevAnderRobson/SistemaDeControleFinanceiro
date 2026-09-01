<?php

namespace App\Enums;

enum EntryStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendente',
            self::Paid => 'Pago / Recebido',
            self::Overdue => 'Vencido',
            self::Cancelled => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 border-amber-200',
            self::Paid => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Overdue => 'bg-rose-50 text-rose-700 border-rose-200',
            self::Cancelled => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
}
