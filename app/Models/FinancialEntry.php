<?php

namespace App\Models;

use App\Enums\EntryStatus;
use App\Enums\EntryType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'type',
        'description',
        'amount',
        'issue_date',
        'due_date',
        'settled_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => EntryType::class,
            'status' => EntryStatus::class,
            'amount' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'settled_at' => 'date',
        ];
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function isPayable(): bool
    {
        return $this->type === EntryType::Payable;
    }

    public function isReceivable(): bool
    {
        return $this->type === EntryType::Receivable;
    }

    public function markAsSettled(?string $date = null): void
    {
        $this->update([
            'status' => EntryStatus::Paid,
            'settled_at' => $date ?: Carbon::today()->toDateString(),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => EntryStatus::Cancelled,
        ]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'R$ ' . number_format((float) $this->amount, 2, ',', '.');
    }

    // Scopes
    public function scopePayables(Builder $query): Builder
    {
        return $query->where('type', EntryType::Payable);
    }

    public function scopeReceivables(Builder $query): Builder
    {
        return $query->where('type', EntryType::Receivable);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', EntryStatus::Pending);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', EntryStatus::Paid);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->where('status', EntryStatus::Overdue)
              ->orWhere(function (Builder $sub) {
                  $sub->where('status', EntryStatus::Pending)
                      ->where('due_date', '<', Carbon::today()->toDateString());
              });
        });
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', EntryStatus::Cancelled);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when(! empty($filters['type']), fn ($q) => $q->where('type', $filters['type']))
            ->when(! empty($filters['status']), function ($q) use ($filters) {
                if ($filters['status'] === EntryStatus::Overdue->value) {
                    $q->overdue();
                } else {
                    $q->where('status', $filters['status']);
                }
            })
            ->when(! empty($filters['person_id']), fn ($q) => $q->where('person_id', $filters['person_id']))
            ->when(! empty($filters['start_date']), fn ($q) => $q->whereDate('due_date', '>=', $filters['start_date']))
            ->when(! empty($filters['end_date']), fn ($q) => $q->whereDate('due_date', '<=', $filters['end_date']))
            ->when(! empty($filters['search']), function ($q) use ($filters) {
                $term = $filters['search'];
                $q->where(function (Builder $sub) use ($term) {
                    $sub->where('description', 'like', "%{$term}%")
                        ->orWhereHas('person', fn ($p) => $p->where('name', 'like', "%{$term}%"));
                });
            });
    }
}
