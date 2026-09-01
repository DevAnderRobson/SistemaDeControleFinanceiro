<?php

namespace App\Http\Requests;

use App\Enums\EntryStatus;
use App\Enums\EntryType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFinancialEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $amount = (string) $this->input('amount');
            if (str_contains($amount, ',')) {
                $amount = str_replace('.', '', $amount);
                $amount = str_replace(',', '.', $amount);
            }
            $this->merge(['amount' => $amount]);
        }
    }

    public function rules(): array
    {
        return [
            'person_id' => ['required', 'exists:people,id'],
            'type' => ['required', Rule::enum(EntryType::class)],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'settled_at' => ['nullable', 'date'],
            'status' => ['nullable', Rule::enum(EntryStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'person_id.required' => 'Selecione a pessoa/empresa',
            'person_id.exists' => 'Pessoa não encontrada',
            'type.required' => 'O tipo é obrigatório',
            'description.required' => 'Informe uma descrição',
            'amount.required' => 'Informe o valor',
            'amount.min' => 'O valor precisa ser maior que zero',
            'issue_date.required' => 'Data de emissão obrigatória',
            'due_date.required' => 'Data de vencimento obrigatória',
            'due_date.after_or_equal' => 'Vencimento não pode ser anterior a emissão',
        ];
    }
}
