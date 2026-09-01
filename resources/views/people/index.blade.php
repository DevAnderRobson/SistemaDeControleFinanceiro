<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pessoas e Empresas') }}
            </h2>
            <a href="{{ route('people.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Novo Cadastro
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                <form method="GET" action="{{ route('people.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2">
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Buscar por nome, documento ou e-mail..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <select name="type" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos os tipos</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" {{ ($filters['type'] ?? '') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-3 py-2 bg-gray-800 text-white text-xs font-semibold rounded-md uppercase hover:bg-gray-700">
                            Filtrar
                        </button>
                        @if(!empty($filters['search']) || !empty($filters['type']))
                            <a href="{{ route('people.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 text-xs font-semibold rounded-md uppercase hover:bg-gray-300">
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-600 font-medium">
                            <tr>
                                <th class="px-4 py-3 text-left">Nome / Razão Social</th>
                                <th class="px-4 py-3 text-left">CPF / CNPJ</th>
                                <th class="px-4 py-3 text-left">Contato</th>
                                <th class="px-4 py-3 text-left">Tipo</th>
                                <th class="px-4 py-3 text-center">Títulos</th>
                                <th class="px-4 py-3 text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($people as $person)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">
                                        {{ $person->name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $person->formatted_document }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        <div>{{ $person->email ?? '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $person->phone ?? '' }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-block px-2 py-0.5 text-xs rounded border {{ $person->type->badgeClass() }}">
                                            {{ $person->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                                        <span title="Total de títulos">{{ $person->entries_count }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                                        <a href="{{ route('people.edit', $person) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-medium">Editar</a>
                                        <form method="POST" action="{{ route('people.destroy', $person) }}" class="inline" onsubmit="return confirm('Deseja realmente excluir este registro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-medium">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        Nenhuma pessoa ou empresa encontrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($people->hasPages())
                    <div class="p-4 border-t border-gray-200">
                        {{ $people->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
