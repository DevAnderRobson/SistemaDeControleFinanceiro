<x-app-layout>
    @php
        $isPayable = $currentType === \App\Enums\EntryType::Payable;
        $title = $isPayable ? 'Contas a Pagar' : 'Contas a Receber';
        $createRoute = $isPayable ? route('payables.create') : route('receivables.create');
        $personLabel = $isPayable ? 'Fornecedor' : 'Cliente';
        $buttonColor = $isPayable ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700';
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __($title) }}
            </h2>
            <a href="{{ $createRoute }}" class="inline-flex items-center px-4 py-2 {{ $buttonColor }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                + Novo Lançamento
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ settleModalOpen: false, selectedEntryId: null, selectedEntryDesc: '', settleDate: '{{ now()->format('Y-m-d') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 text-sm">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Descrição ou nome..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ $personLabel }}</label>
                        <select name="person_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}" {{ ($filters['person_id'] ?? '') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos os status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Vencimento Início</label>
                        <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Vencimento Fim</label>
                        <div class="flex space-x-2">
                            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-xs font-semibold rounded-md uppercase hover:bg-gray-700">
                                Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-medium">
                            <tr>
                                <th class="px-4 py-3 text-left">Descrição</th>
                                <th class="px-4 py-3 text-left">{{ $personLabel }}</th>
                                <th class="px-4 py-3 text-right">Valor</th>
                                <th class="px-4 py-3 text-center">Emissão</th>
                                <th class="px-4 py-3 text-center">Vencimento</th>
                                <th class="px-4 py-3 text-center">Data Baixa</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($entries as $entry)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $entry->description }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $entry->person->name }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $isPayable ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $entry->formatted_amount }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                        {{ $entry->issue_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs {{ ($entry->status === \App\Enums\EntryStatus::Pending && $entry->due_date->isPast()) ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                                        {{ $entry->due_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-gray-500 text-xs">
                                        {{ $entry->settled_at ? $entry->settled_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 text-xs rounded border {{ $entry->status->badgeClass() }}">
                                            {{ $entry->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                        @if($entry->status !== \App\Enums\EntryStatus::Paid && $entry->status !== \App\Enums\EntryStatus::Cancelled)
                                            <button type="button" @click="settleModalOpen = true; selectedEntryId = {{ $entry->id }}; selectedEntryDesc = '{{ addslashes($entry->description) }}'" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold">
                                                {{ $isPayable ? 'Pagar' : 'Receber' }}
                                            </button>
                                        @endif

                                        <a href="{{ $isPayable ? route('payables.edit', $entry) : route('receivables.edit', $entry) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Editar</a>

                                        @if($entry->status !== \App\Enums\EntryStatus::Paid && $entry->status !== \App\Enums\EntryStatus::Cancelled)
                                            <form method="POST" action="{{ route('entries.cancel', $entry) }}" class="inline" onsubmit="return confirm('Deseja realmente cancelar este título financeiro?');">
                                                @csrf
                                                <button type="submit" class="text-amber-600 hover:text-amber-900 text-xs font-medium">Cancelar</button>
                                            </form>
                                        @endif

                                        @if($entry->status !== \App\Enums\EntryStatus::Paid)
                                            <form method="POST" action="{{ $isPayable ? route('payables.destroy', $entry) : route('receivables.destroy', $entry) }}" class="inline" onsubmit="return confirm('Deseja realmente excluir este lançamento?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">Excluir</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                        Nenhum lançamento encontrado para os filtros selecionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($entries->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $entries->links() }}
                    </div>
                @endif
            </div>

        </div>

        <div x-show="settleModalOpen" x-cloak style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="settleModalOpen = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-2" id="modal-title">
                        Confirmar Baixa de Título
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Título: <strong x-text="selectedEntryDesc"></strong>
                    </p>

                    <form :action="'/entries/' + selectedEntryId + '/settle'" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Data da Baixa ({{ $isPayable ? 'Pagamento' : 'Recebimento' }}) *</label>
                            <input type="date" name="settled_at" x-model="settleDate" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div class="flex justify-end space-x-2 pt-3 border-t">
                            <button type="button" @click="settleModalOpen = false" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md uppercase hover:bg-gray-200">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-md uppercase hover:bg-emerald-700">
                                Confirmar Baixa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
