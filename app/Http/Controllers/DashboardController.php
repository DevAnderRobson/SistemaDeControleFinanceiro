<?php

namespace App\Http\Controllers;

use App\Enums\EntryStatus;
use App\Models\FinancialEntry;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        $totalToReceive = (float) FinancialEntry::receivables()
            ->whereIn('status', [EntryStatus::Pending, EntryStatus::Overdue])
            ->sum('amount');

        $totalReceived = (float) FinancialEntry::receivables()
            ->paid()
            ->sum('amount');

        $totalOverdueReceive = (float) FinancialEntry::receivables()
            ->overdue()
            ->sum('amount');

        $totalToPay = (float) FinancialEntry::payables()
            ->whereIn('status', [EntryStatus::Pending, EntryStatus::Overdue])
            ->sum('amount');

        $totalPaid = (float) FinancialEntry::payables()
            ->paid()
            ->sum('amount');

        $totalOverduePay = (float) FinancialEntry::payables()
            ->overdue()
            ->sum('amount');

        $expectedBalance = ($totalReceived + $totalToReceive) - ($totalPaid + $totalToPay);
        $realizedBalance = $totalReceived - $totalPaid;

        $recentEntries = FinancialEntry::with('person')
            ->latest('id')
            ->limit(5)
            ->get();

        $upcomingEntries = FinancialEntry::with('person')
            ->whereIn('status', [EntryStatus::Pending, EntryStatus::Overdue])
            ->whereBetween('due_date', [$today, Carbon::today()->addDays(7)->toDateString()])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        $monthlyLabels = [];
        $monthlyReceivedData = [];
        $monthlyPaidData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $startOfMonth = $monthDate->copy()->startOfMonth()->toDateString();
            $endOfMonth = $monthDate->copy()->endOfMonth()->toDateString();
            $monthlyLabels[] = $monthDate->translatedFormat('M/Y');

            $receivedInMonth = (float) FinancialEntry::receivables()
                ->paid()
                ->whereBetween('settled_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $paidInMonth = (float) FinancialEntry::payables()
                ->paid()
                ->whereBetween('settled_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');

            $monthlyReceivedData[] = $receivedInMonth;
            $monthlyPaidData[] = $paidInMonth;
        }

        return view('dashboard', [
            'metrics' => [
                'totalToReceive' => $totalToReceive,
                'totalReceived' => $totalReceived,
                'totalOverdueReceive' => $totalOverdueReceive,
                'totalToPay' => $totalToPay,
                'totalPaid' => $totalPaid,
                'totalOverduePay' => $totalOverduePay,
                'expectedBalance' => $expectedBalance,
                'realizedBalance' => $realizedBalance,
            ],
            'recentEntries' => $recentEntries,
            'upcomingEntries' => $upcomingEntries,
            'chartData' => [
                'labels' => $monthlyLabels,
                'received' => $monthlyReceivedData,
                'paid' => $monthlyPaidData,
            ],
        ]);
    }
}
