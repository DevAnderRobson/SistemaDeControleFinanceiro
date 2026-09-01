<x-app-layout>
    @php
        $isPayable = $entry->type === \App\Enums\EntryType::Payable;
        $title = $isPayable ? 'Editar Conta a Pagar' : 'Editar Conta a Receber';
        $backRoute = $isPayable ? route('payables.index') : route('receivables.index');
        $updateRoute = $isPayable ? route('payables.update', $entry) : route('receivables.update', $entry);
        $personLabel = $isPayable ? 'Fornecedor' : 'Cliente';
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __($title) }}
            </h2>
            <a href="{{ $backRoute }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Voltar para a lista
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <form method="POST" action="{{ $updateRoute }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $entry->type->value }}">

                    <div>
                        <label for="person_id" class="block text-sm font-medium text-gray-700">{{ $personLabel }} *</label>
                        <select id="person_id" name="person_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @foreach ($people as $person)
                                <option value="{{ $person->id }}" {{ old('person_id', $entry->person_id) == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} ({{ $person->formatted_document }})
                                </option>
                            @endforeach
                        </select>
                        @error('person_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Descrição do Lançamento *</label>
                        <input type="text" id="description" name="description" value="{{ old('description', $entry->description) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('description')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Valor (R$) *</label>
                            <input type="text" id="amount" name="amount" value="{{ old('amount', number_format((float) $entry->amount, 2, ',', '.')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('amount')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="issue_date" class="block text-sm font-medium text-gray-700">Data de Emissão *</label>
                            <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', $entry->issue_date->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('issue_date')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Data de Vencimento *</label>
                            <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $entry->due_date->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('due_date')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status', $entry->status->value) === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="settled_at" class="block text-sm font-medium text-gray-700">Data de Baixa (Pagamento/Recebimento)</label>
                            <input type="date" id="settled_at" name="settled_at" value="{{ old('settled_at', $entry->settled_at?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('settled_at')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Observações Adicionais</label>
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('notes', $entry->notes) }}</textarea>
                        @error('notes')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ $backRoute }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md uppercase hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md uppercase hover:bg-indigo-700">
                            Atualizar Lançamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
