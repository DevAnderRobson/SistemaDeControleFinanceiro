<?php

namespace App\Http\Controllers;

use App\Enums\EntryStatus;
use App\Enums\EntryType;
use App\Http\Requests\SettleFinancialEntryRequest;
use App\Http\Requests\StoreFinancialEntryRequest;
use App\Http\Requests\UpdateFinancialEntryRequest;
use App\Models\FinancialEntry;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialEntryController extends Controller
{
    public function index(Request $request): View
    {
        $currentType = $this->resolveType($request);

        $entries = FinancialEntry::query()
            ->with('person')
            ->when($currentType, fn ($q) => $q->where('type', $currentType))
            ->filter($request->only(['status', 'person_id', 'start_date', 'end_date', 'search']))
            ->orderBy('due_date')
            ->paginate(15)
            ->withQueryString();

        $people = $currentType === EntryType::Payable
            ? Person::suppliers()->orderBy('name')->get()
            : ($currentType === EntryType::Receivable ? Person::customers()->orderBy('name')->get() : Person::orderBy('name')->get());

        return view('entries.index', [
            'entries' => $entries,
            'currentType' => $currentType,
            'statuses' => EntryStatus::cases(),
            'people' => $people,
            'filters' => $request->only(['status', 'person_id', 'start_date', 'end_date', 'search']),
        ]);
    }

    public function create(Request $request): View
    {
        $currentType = $this->resolveType($request) ?? EntryType::Receivable;

        $people = $currentType === EntryType::Payable
            ? Person::suppliers()->orderBy('name')->get()
            : Person::customers()->orderBy('name')->get();

        return view('entries.create', [
            'currentType' => $currentType,
            'people' => $people,
        ]);
    }

    public function store(StoreFinancialEntryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['settled_at'])) {
            $data['status'] = EntryStatus::Paid;
        } else {
            $data['status'] = EntryStatus::Pending;
        }

        $entry = FinancialEntry::create($data);

        $route = $entry->type === EntryType::Payable ? 'payables.index' : 'receivables.index';
        $label = $entry->type === EntryType::Payable ? "Conta a pagar" : 'Conta a receber';

        return redirect()
            ->route($route)
            ->with('success', "{$label} cadastrada com sucesso.");
    }

    public function edit(FinancialEntry $entry): View
    {
        $people = $entry->type === EntryType::Payable
            ? Person::suppliers()->orderBy('name')->get()
            : Person::customers()->orderBy('name')->get();

        return view('entries.edit', [
            'entry' => $entry,
            'currentType' => $entry->type,
            'people' => $people,
            'statuses' => EntryStatus::cases(),
        ]);
    }

    public function update(UpdateFinancialEntryRequest $request, FinancialEntry $entry): RedirectResponse
    {
        $entry->update($request->validated());

        $route = $entry->type === EntryType::Payable ? 'payables.index' : 'receivables.index';

        return redirect()
            ->route($route)
            ->with('success', "Lançamento financeiro atualizado com sucesso");
    }

    public function destroy(FinancialEntry $entry): RedirectResponse
    {
        $route = $entry->type === EntryType::Payable ? 'payables.index' : 'receivables.index';

        if ($entry->status === EntryStatus::Paid) {
            return redirect()
                ->route($route)
                ->with('error', 'Não é possível excluir um título que já foi pago ou recebido');
        }

        $entry->delete();

        return redirect()
            ->route($route)
            ->with('success', 'Lançamento financeiro excluído com sucesso.');
    }

    public function settle(SettleFinancialEntryRequest $request, FinancialEntry $entry): RedirectResponse
    {
        if ($entry->status === EntryStatus::Cancelled) {
            return back()->with('error', 'Não é possível dar baixa em título cancelado');
        }

        $entry->markAsSettled($request->validated('settled_at'));

        $msg = $entry->type === EntryType::Payable ? 'Pagamento registrado com sucesso' : "Recebimento registrado com sucesso!";

        return back()->with('success', $msg);
    }

    public function cancel(FinancialEntry $entry): RedirectResponse
    {
        if ($entry->status === EntryStatus::Paid) {
            return back()->with('error', 'Não é possível cancelar um título que já foi pago ou recebido.');
        }

        $entry->markAsCancelled();

        return back()->with('success', "Título financeiro cancelado com sucesso");
    }

    private function resolveType(Request $request): ?EntryType
    {
        if ($request->routeIs('payables.*') || $request->input('type') === 'payable') {
            return EntryType::Payable;
        }

        if ($request->routeIs('receivables.*') || $request->input('type') === 'receivable') {
            return EntryType::Receivable;
        }

        return null;
    }
}
