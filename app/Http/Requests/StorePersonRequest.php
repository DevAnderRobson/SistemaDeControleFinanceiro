<?php

namespace App\Http\Requests;

use App\Enums\PersonType;
use App\Rules\CpfCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('document')) {
            $this->merge([
                'document' => strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', (string) $this->input('document'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => ['required', 'string', 'unique:people,document', new CpfCnpj],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'type' => ['required', Rule::enum(PersonType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome ou razão social é obrigatório',
            'document.required' => 'O CPF ou CNPJ é obrigatório',
            'document.unique' => 'Este CPF/CNPJ já foi cadastrado',
            'email.email' => 'Informe um e-mail válido',
            'type.required' => 'Selecione se é Cliente ou Fornecedor',
        ];
    }
}
