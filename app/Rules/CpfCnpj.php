<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('O campo deve ser um texto');
            return;
        }

        $cleanValue = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $value));
        $length = strlen($cleanValue);

        if ($length === 11) {
            if (! ctype_digit($cleanValue) || ! $this->validateCpf($cleanValue)) {
                $fail('O CPF informado não é válido');
            }
            return;
        }

        if ($length === 14) {
            if (! $this->validateCnpj($cleanValue)) {
                $fail("O CNPJ informado não é válido.");
            }
            return;
        }

        $fail('Informe um CPF (11 dígitos) ou CNPJ (14 dígitos) válido');
    }

    private function validateCpf(string $cpf): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        if ((int) $cpf[9] !== $digit1) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        return (int) $cpf[10] === $digit2;
    }

    private function validateCnpj(string $cnpj): bool
    {
        if (preg_match('/^(.)\1{13}$/', $cnpj)) {
            return false;
        }

        if (! is_numeric($cnpj[12]) || ! is_numeric($cnpj[13])) {
            return false;
        }

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $val = ord($cnpj[$i]) - 48;
            $sum += $val * $weights1[$i];
        }
        $remainder = $sum % 11;
        $digit1 = ($remainder < 2) ? 0 : 11 - $remainder;
        if ((int) $cnpj[12] !== $digit1) {
            return false;
        }

        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $val = ord($cnpj[$i]) - 48;
            $sum += $val * $weights2[$i];
        }
        $sum += $digit1 * $weights2[12];
        $remainder = $sum % 11;
        $digit2 = ($remainder < 2) ? 0 : 11 - $remainder;

        return (int) $cnpj[13] === $digit2;
    }
}
