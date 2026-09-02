<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Gerencial') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('receivables.create') }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:outline-none">
                    + Nova Receita
                </a>
                <a href="{{ route('payables.create') }}" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none">
                    + Nova Despesa
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Saldo Previsto</span>
                    <div class="text-2xl font-bold mt-1 {{ $metrics['expectedBalance'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        R$ {{ number_format($metrics['expectedBalance'], 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Considera total já realizado + pendente a receber e a pagar</p>
                </div>

                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500">Saldo Realizado</span>
                    <div class="text-2xl font-bold mt-1 {{ $metrics['realizedBalance'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        R$ {{ number_format($metrics['realizedBalance'], 2, ',', '.') }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Total recebido em caixa menos total pago</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h3 class="font-semibold text-gray-800">Contas a Receber</h3>
                        <a href="{{ route('receivables.index') }}" class="text-xs text-emerald-600 hover:underline">Ver todas &rarr;</a>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-gray-50 p-3 rounded">
                            <span class="text-xs text-gray-500 block">A Receber</span>
                            <span class="font-semibold text-sm text-gray-800 block mt-1">
                                R$ {{ number_format($metrics['totalToReceive'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="bg-emerald-50 p-3 rounded border border-emerald-100">
                            <span class="text-xs text-emerald-700 block font-medium">Recebido</span>
                            <span class="font-semibold text-sm text-emerald-800 block mt-1">
                                R$ {{ number_format($metrics['totalReceived'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="bg-rose-50 p-3 rounded border border-rose-100">
                            <span class="text-xs text-rose-700 block font-medium">Vencido</span>
                            <span class="font-semibold text-sm text-rose-800 block mt-1">
                                R$ {{ number_format($metrics['totalOverdueReceive'], 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Coluna de Contas a Pagar -->
                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h3 class="font-semibold text-gray-800">Contas a Pagar</h3>
                        <a href="{{ route('payables.index') }}" class="text-xs text-red-600 hover:underline">Ver todas &rarr;</a>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-gray-50 p-3 rounded">
                            <span class="text-xs text-gray-500 block">A Pagar</span>
                            <span class="font-semibold text-sm text-gray-800 block mt-1">
                                R$ {{ number_format($metrics['totalToPay'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="bg-red-50 p-3 rounded border border-red-100">
                            <span class="text-xs text-red-700 block font-medium">Pago</span>
                            <span class="font-semibold text-sm text-red-800 block mt-1">
                                R$ {{ number_format($metrics['totalPaid'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="bg-rose-50 p-3 rounded border border-rose-100">
                            <span class="text-xs text-rose-700 block font-medium">Vencido</span>
                            <span class="font-semibold text-sm text-rose-800 block mt-1">
                                R$ {{ number_format($metrics['totalOverduePay'], 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                <h3 class="font-semibold text-gray-800 mb-4">Fluxo Realizado (Últimos 6 Meses)</h3>
                <div class="h-64">
                    <canvas id="monthlyFlowChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-800 mb-3">Próximos Vencimentos (7 dias)</h3>
                    @if ($upcomingEntries->isEmpty())
                        <p class="text-sm text-gray-500 py-4 text-center">Nenhum título com vencimento próximo.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($upcomingEntries as $entry)
                                <div class="py-2.5 flex justify-between items-center text-sm">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $entry->description }}</p>
                                        <p class="text-xs text-gray-500">{{ $entry->person->name }} &bull; Vence em {{ $entry->due_date->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold block {{ $entry->isPayable() ? 'text-red-600' : 'text-emerald-600' }}">
                                            {{ $entry->formatted_amount }}
                                        </span>
                                        <span class="inline-block px-1.5 py-0.5 text-xs rounded {{ $entry->status->badgeClass() }}">
                                            {{ $entry->status->label() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
                    <h3 class="font-semibold text-gray-800 mb-3">Lançamentos Recentes</h3>
                    @if ($recentEntries->isEmpty())
                        <p class="text-sm text-gray-500 py-4 text-center">Nenhum lançamento registrado até o momento.</p>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($recentEntries as $entry)
                                <div class="py-2.5 flex justify-between items-center text-sm">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $entry->description }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $entry->type->label() }} &bull; {{ $entry->person->name }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold block {{ $entry->isPayable() ? 'text-red-600' : 'text-emerald-600' }}">
                                            {{ $entry->formatted_amount }}
                                        </span>
                                        <span class="inline-block px-1.5 py-0.5 text-xs rounded {{ $entry->status->badgeClass() }}">
                                            {{ $entry->status->label() }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('monthlyFlowChart');
                if (!ctx) return;

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [
                            {
                                label: 'Recebido (R$)',
                                data: @json($chartData['received']),
                                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                                borderColor: 'rgb(16, 185, 129)',
                                borderWidth: 1
                            },
                            {
                                label: 'Pago (R$)',
                                data: @json($chartData['paid']),
                                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                                borderColor: 'rgb(239, 68, 68)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
