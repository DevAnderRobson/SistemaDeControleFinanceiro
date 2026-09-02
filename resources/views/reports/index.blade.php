<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Relatório Financeiro Gerencial') }}
            </h2>
            <a href="{{ route('reports.export.csv', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Exportar CSV (Excel)
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Filtros do Relatório</h3>
                <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 text-sm">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tipo de Título</label>
                        <select name="type" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos (Pagar e Receber)</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" {{ ($filters['type'] ?? '') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
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

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pessoa / Empresa</label>
                        <select name="person_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas as pessoas / empresas</option>
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}" {{ ($filters['person_id'] ?? '') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} ({{ $person->type->label() }})
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
                        <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="lg:col-span-6 flex justify-end space-x-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('reports.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md uppercase hover:bg-gray-200">
                            Limpar Filtros
                        </a>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-md uppercase hover:bg-gray-700">
                            Aplicar Filtros
                        </button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm text-center">
                    <span class="text-xs text-gray-500 block">Qtd. Registros</span>
                    <span class="text-lg font-bold text-gray-800 block mt-1">{{ $summary['count'] }}</span>
                </div>
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm text-center">
                    <span class="text-xs text-emerald-600 font-medium block">Total a Receber</span>
                    <span class="text-sm font-bold text-emerald-700 block mt-1">R$ {{ number_format($summary['totalReceivable'], 2, ',', '.') }}</span>
                </div>
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm text-center">
                    <span class="text-xs text-red-600 font-medium block">Total a Pagar</span>
                    <span class="text-sm font-bold text-red-700 block mt-1">R$ {{ number_format($summary['totalPayable'], 2, ',', '.') }}</span>
                </div>
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm text-center">
                    <span class="text-xs text-indigo-600 font-medium block">Total Liquidado</span>
                    <span class="text-sm font-bold text-indigo-700 block mt-1">R$ {{ number_format($summary['totalPaid'], 2, ',', '.') }}</span>
                </div>
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm text-center">
                    <span class="text-xs text-rose-600 font-medium block">Total Vencido</span>
                    <span class="text-sm font-bold text-rose-700 block mt-1">R$ {{ number_format($summary['totalOverdue'], 2, ',', '.') }}</span>
                </div>
                <div class="bg-white p-3 rounded-lg border border-gray-200 shadow-sm text-center">
                    <span class="text-xs text-gray-500 font-medium block">Saldo do Filtro</span>
                    <span class="text-sm font-bold block mt-1 {{ $summary['netBalance'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        R$ {{ number_format($summary['netBalance'], 2, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-medium">
                            <tr>
                                <th class="px-4 py-3 text-left">Tipo</th>
                                <th class="px-4 py-3 text-left">Pessoa / Empresa</th>
                                <th class="px-4 py-3 text-left">Descrição</th>
                                <th class="px-4 py-3 text-right">Valor</th>
                                <th class="px-4 py-3 text-center">Emissão</th>
                                <th class="px-4 py-3 text-center">Vencimento</th>
                                <th class="px-4 py-3 text-center">Data Baixa</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($entries as $entry)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-0.5 text-xs rounded border {{ $entry->type->badgeClass() }}">
                                            {{ $entry->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800 font-medium">
                                        {{ $entry->person->name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $entry->description }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $entry->isPayable() ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $entry->formatted_amount }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                                        {{ $entry->issue_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs {{ ($entry->status === \App\Enums\EntryStatus::Pending && $entry->due_date->isPast()) ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                                        {{ $entry->due_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                                        {{ $entry->settled_at ? $entry->settled_at->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block px-2 py-0.5 text-xs rounded border {{ $entry->status->badgeClass() }}">
                                            {{ $entry->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum registro encontrado com os filtros aplicados.
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
    </div>
</x-app-layout>
