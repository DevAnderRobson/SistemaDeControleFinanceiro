<?php

namespace App\Models;

use App\Enums\EntryType;
use App\Enums\PersonType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'document',
        'email',
        'phone',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => PersonType::class,
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(FinancialEntry::class);
    }

    public function payables(): HasMany
    {
        return $this->hasMany(FinancialEntry::class)->where('type', EntryType::Payable);
    }

    public function receivables(): HasMany
    {
        return $this->hasMany(FinancialEntry::class)->where('type', EntryType::Receivable);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $cleanTerm = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $term));

        return $query->where(function (Builder $q) use ($term, $cleanTerm) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('document', 'like', "%{$term}%");

            if (! empty($cleanTerm)) {
                $q->orWhere('document', 'like', "%{$cleanTerm}%");
            }
        });
    }

    public function scopeCustomers(Builder $query): Builder
    {
        return $query->whereIn('type', [PersonType::Customer]);
    }

    public function scopeSuppliers(Builder $query): Builder
    {
        return $query->whereIn('type', [PersonType::Supplier]);
    }

    public function getFormattedDocumentAttribute(): string
    {
        $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string) $this->document));

        if (strlen($clean) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $clean);
        }

        if (strlen($clean) === 14) {
            return preg_replace('/([a-zA-Z0-9]{2})([a-zA-Z0-9]{3})([a-zA-Z0-9]{3})([a-zA-Z0-9]{4})([a-zA-Z0-9]{2})/', '$1.$2.$3/$4-$5', $clean);
        }

        return (string) $this->document;
    }
}
