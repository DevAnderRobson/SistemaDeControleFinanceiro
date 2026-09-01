<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Pessoa / Empresa') }}
            </h2>
            <a href="{{ route('people.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                &larr; Voltar para a lista
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                <form method="POST" action="{{ route('people.update', $person) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome ou Razão Social *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $person->name) }}" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('name')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div x-data="{
                            doc: '{{ old('document', $person->formatted_document) }}',
                            mask(val) {
                                let v = (val || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
                                if (v.length <= 11 && /^\d+$/.test(v)) {
                                    return v
                                        .replace(/(\d{3})(\d)/, '$1.$2')
                                        .replace(/(\d{3})(\d)/, '$1.$2')
                                        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                                } else {
                                    v = v.substring(0, 14);
                                    return v
                                        .replace(/^([A-Z0-9]{2})([A-Z0-9])/, '$1.$2')
                                        .replace(/^([A-Z0-9]{2})\.([A-Z0-9]{3})([A-Z0-9])/, '$1.$2.$3')
                                        .replace(/\.([A-Z0-9]{3})([A-Z0-9])/, '.$1/$2')
                                        .replace(/([A-Z0-9]{4})([A-Z0-9]{1,2})$/, '$1-$2');
                                }
                            }
                        }">
                            <label for="document" class="block text-sm font-medium text-gray-700">CPF ou CNPJ *</label>
                            <input type="text" id="document" name="document" x-model="doc" @input="doc = mask($event.target.value)" required maxlength="18" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('document')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipo de Cadastro *</label>
                            <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" {{ old('type', $person->type->value) === $type->value ? 'selected' : '' }}>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $person->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('email')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telefone / Celular</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $person->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            @error('phone')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('people.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md uppercase hover:bg-gray-200">
                            Cancelar
                        </a>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-md uppercase hover:bg-indigo-700">
                            Atualizar Cadastro
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
