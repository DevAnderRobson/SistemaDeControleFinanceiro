<?php

namespace App\Http\Controllers;

use App\Enums\PersonType;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function index(Request $request): View
    {
        $people = Person::query()
            ->search($request->input('search'))
            ->when($request->input('type'), fn ($q, $type) => $q->where('type', $type))
            ->withCount(['entries', 'payables', 'receivables'])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('people.index', [
            'people' => $people,
            'types' => PersonType::cases(),
            'filters' => $request->only(['search', 'type']),
        ]);
    }

    public function create(): View
    {
        return view('people.create', [
            'types' => PersonType::cases(),
        ]);
    }

    public function store(StorePersonRequest $request): RedirectResponse
    {
        $person = Person::create($request->validated());

        return redirect()
            ->route('people.index')
            ->with('success', "Pessoa/Empresa '{$person->name}' cadastrada com sucesso.");
    }

    public function edit(Person $person): View
    {
        return view('people.edit', [
            'person' => $person,
            'types' => PersonType::cases(),
        ]);
    }

    public function update(UpdatePersonRequest $request, Person $person): RedirectResponse
    {
        $person->update($request->validated());

        return redirect()
            ->route('people.index')
            ->with('success', "Dados de '{$person->name}' atualizados com sucesso.");
    }

    public function destroy(Person $person): RedirectResponse
    {
        if ($person->entries()->exists()) {
            return redirect()
                ->route('people.index')
                ->with('error', 'Não é possível excluir esta pessoa/empresa porque existem lançamentos financeiros vinculados a ela.');
        }

        $person->delete();

        return redirect()
            ->route('people.index')
            ->with('success', 'Registro excluído com sucesso.');
    }
}
