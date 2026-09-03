<?php

namespace Database\Seeders;

use App\Enums\EntryStatus;
use App\Enums\EntryType;
use App\Enums\PersonType;
use App\Models\FinancialEntry;
use App\Models\Person;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FinancialControlSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@financeiro.local'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('senha123'),
                'email_verified_at' => now(),
            ]
        );

        $peopleData = [
            [
                'name' => 'TechSolutions Informática Ltda',
                'document' => '04252011000110',
                'email' => 'financeiro@techsolutions.com.br',
                'phone' => '(11) 3456-7890',
                'type' => PersonType::Customer,
            ],
            [
                'name' => 'Consultoria Alfa & Associados',
                'document' => '11222333000181',
                'email' => 'contato@consultoriaalfa.com.br',
                'phone' => '(21) 2233-4455',
                'type' => PersonType::Customer,
            ],
            [
                'name' => 'João Carlos da Silva',
                'document' => '22616239077',
                'email' => 'joao.carlos@email.com',
                'phone' => '(19) 98765-4321',
                'type' => PersonType::Customer,
            ],
            [
                'name' => 'Amazon Web Services Serviços de Nuvem',
                'document' => '23412247000110',
                'email' => 'billing@aws-nuvem.com.br',
                'phone' => '(11) 4004-9000',
                'type' => PersonType::Supplier,
            ],
            [
                'name' => 'Imobiliária Central Paulistana',
                'document' => '33445566000186',
                'email' => 'locacao@imobiliariacentral.com.br',
                'phone' => '(11) 3100-2000',
                'type' => PersonType::Supplier,
            ],
            [
                'name' => 'Telefônica Telecomunicações S/A',
                'document' => '02558157000162',
                'email' => 'empresas@telefonicabr.com.br',
                'phone' => '(11) 3003-8888',
                'type' => PersonType::Supplier,
            ]
        ];

        $createdPeople = [];
        foreach ($peopleData as $data) {
            $createdPeople[$data['name']] = Person::updateOrCreate(
                ['document' => $data['document']],
                $data
            );
        }

        $today = Carbon::today();

        $receivables = [
            [
                'person' => 'TechSolutions Informática Ltda',
                'description' => 'Contrato Mensal de Suporte e Desenvolvimento',
                'amount' => 8500.00,
                'issue_date' => $today->copy()->subMonths(1)->startOfMonth()->toDateString(),
                'due_date' => $today->copy()->subMonths(1)->day(15)->toDateString(),
                'settled_at' => $today->copy()->subMonths(1)->day(14)->toDateString(),
                'status' => EntryStatus::Paid,
            ],
            [
                'person' => 'Consultoria Alfa & Associados',
                'description' => 'Licença Semestral de Software ERP',
                'amount' => 12400.00,
                'issue_date' => $today->copy()->subDays(45)->toDateString(),
                'due_date' => $today->copy()->subDays(15)->toDateString(),
                'settled_at' => $today->copy()->subDays(12)->toDateString(),
                'status' => EntryStatus::Paid,
            ],
            [
                'person' => 'TechSolutions Informática Ltda',
                'description' => 'Contrato Mensal de Suporte - Mês Atual',
                'amount' => 8500.00,
                'issue_date' => $today->copy()->startOfMonth()->toDateString(),
                'due_date' => $today->copy()->day(15)->toDateString(),
                'settled_at' => null,
                'status' => EntryStatus::Pending,
            ],
            [
                'person' => 'João Carlos da Silva',
                'description' => 'Treinamento Especializado em Laravel 13',
                'amount' => 2200.00,
                'issue_date' => $today->copy()->subDays(5)->toDateString(),
                'due_date' => $today->copy()->addDays(10)->toDateString(),
                'settled_at' => null,
                'status' => EntryStatus::Pending,
            ]
        ];

        foreach ($receivables as $item) {
            FinancialEntry::create([
                'person_id' => $createdPeople[$item['person']]->id,
                'type' => EntryType::Receivable,
                'description' => $item['description'],
                'amount' => $item['amount'],
                'issue_date' => $item['issue_date'],
                'due_date' => $item['due_date'],
                'settled_at' => $item['settled_at'],
                'status' => $item['status'],
            ]);
        }

        $payables = [
            [
                'person' => 'Amazon Web Services Serviços de Nuvem',
                'description' => 'Servidores de Produção e Banco MySQL',
                'amount' => 1750.00,
                'issue_date' => $today->copy()->subMonths(1)->startOfMonth()->toDateString(),
                'due_date' => $today->copy()->subMonths(1)->day(10)->toDateString(),
                'settled_at' => $today->copy()->subMonths(1)->day(9)->toDateString(),
                'status' => EntryStatus::Paid,
            ],
            [
                'person' => 'Imobiliária Central Paulistana',
                'description' => 'Aluguel do Escritório e Condomínio Comercial',
                'amount' => 4200.00,
                'issue_date' => $today->copy()->subDays(20)->toDateString(),
                'due_date' => $today->copy()->subDays(5)->toDateString(),
                'settled_at' => $today->copy()->subDays(5)->toDateString(),
                'status' => EntryStatus::Paid,
            ],
            [
                'person' => 'Telefônica Telecomunicações S/A',
                'description' => 'Link Dedicado de Fibra Óptica 1Gbps',
                'amount' => 890.00,
                'issue_date' => $today->copy()->startOfMonth()->toDateString(),
                'due_date' => $today->copy()->addDays(5)->toDateString(),
                'settled_at' => null,
                'status' => EntryStatus::Pending,
            ],
            [
                'person' => 'Amazon Web Services Serviços de Nuvem',
                'description' => 'Infraestrutura Cloud Mês Corrente',
                'amount' => 1890.00,
                'issue_date' => $today->copy()->startOfMonth()->toDateString(),
                'due_date' => $today->copy()->addDays(12)->toDateString(),
                'settled_at' => null,
                'status' => EntryStatus::Pending,
            ],
            [
                'person' => 'Imobiliária Central Paulistana',
                'description' => 'Aluguel de Vagas de Garagem Adicionais',
                'amount' => 600.00,
                'issue_date' => $today->copy()->subDays(35)->toDateString(),
                'due_date' => $today->copy()->subDays(7)->toDateString(),
                'settled_at' => null,
                'status' => EntryStatus::Overdue,
            ],
        ];

        foreach ($payables as $item) {
            FinancialEntry::create([
                'person_id' => $createdPeople[$item['person']]->id,
                'type' => EntryType::Payable,
                'description' => $item['description'],
                'amount' => $item['amount'],
                'issue_date' => $item['issue_date'],
                'due_date' => $item['due_date'],
                'settled_at' => $item['settled_at'],
                'status' => $item['status'],
            ]);
        }
    }
}
