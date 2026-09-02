<?php

namespace App\Http\Controllers;

use App\Enums\EntryStatus;
use App\Enums\EntryType;
use App\Models\FinancialEntry;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->extractFilters($request);

        $query = FinancialEntry::with('person')->filter($filters);

        $totalsQuery = clone $query;
        $totalPayable = (clone $totalsQuery)->payables()->sum('amount');
        $totalReceivable = (clone $totalsQuery)->receivables()->sum('amount');
        $totalPaid = (clone $totalsQuery)->paid()->sum('amount');
        $totalPending = (clone $totalsQuery)->pending()->sum('amount');
        $totalOverdue = (clone $totalsQuery)->overdue()->sum('amount');
        $netBalance = $totalReceivable - $totalPayable;

        $entries = $query->orderBy('due_date')->paginate(20)->withQueryString();

        return view('reports.index', [
            'entries' => $entries,
            'people' => Person::orderBy('name')->get(),
            'types' => EntryType::cases(),
            'statuses' => EntryStatus::cases(),
            'filters' => $filters,
            'summary' => [
                'totalPayable' => (float) $totalPayable,
                'totalReceivable' => (float) $totalReceivable,
                'totalPaid' => (float) $totalPaid,
                'totalPending' => (float) $totalPending,
                'totalOverdue' => (float) $totalOverdue,
                'netBalance' => (float) $netBalance,
                'count' => $totalsQuery->count(),
            ],
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $this->extractFilters($request);
        $entries = FinancialEntry::with('person')
            ->filter($filters)
            ->orderBy('due_date')
            ->get();

        $fileName = 'relatorio_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($entries) {
            $handle = fopen('php://output', 'w');
            
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Tipo',
                'Pessoa / Empresa',
                'Documento',
                'Descrição',
                'Valor',
                'Emissão',
                'Vencimento',
                'Baixa',
                'Status',
            ], ';');

            foreach ($entries as $entry) {
                fputcsv($handle, [
                    $entry->id,
                    $entry->type->label(),
                    $entry->person?->name ?? '-',
                    $entry->person?->formatted_document ?? '-',
                    $entry->description,
                    number_format((float) $entry->amount, 2, ',', '.'),
                    $entry->issue_date->format('d/m/Y'),
                    $entry->due_date->format('d/m/Y'),
                    $entry->settled_at ? $entry->settled_at->format('d/m/Y') : '-',
                    $entry->status->label(),
                ], ';');
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function extractFilters(Request $request): array
    {
        return [
            'type' => $request->input('type'),
            'status' => $request->input('status'),
            'person_id' => $request->input('person_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'search' => $request->input('search'),
        ];
    }
}
