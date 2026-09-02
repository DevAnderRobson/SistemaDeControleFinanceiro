<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettleFinancialEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settled_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'settled_at.required' => 'A data de quitação (pagamento/recebimento) é obrigatória.',
            'settled_at.date' => 'Informe uma data de quitação válida.',
        ];
    }
}
