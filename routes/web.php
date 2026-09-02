<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialEntryController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth'])->group(function () {
    // Dashboard Gerencial
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cadastro de Pessoas / Empresas (Clientes e Fornecedores)
    Route::resource('people', PersonController::class);

    // Contas a Pagar
    Route::resource('payables', FinancialEntryController::class)->parameters(['payables' => 'entry']);

    // Contas a Receber
    Route::resource('receivables', FinancialEntryController::class)->parameters(['receivables' => 'entry']);

    // Operações de Quitação e Cancelamento de Títulos
    Route::post('/entries/{entry}/settle', [FinancialEntryController::class, 'settle'])->name('entries.settle');
    Route::post('/entries/{entry}/cancel', [FinancialEntryController::class, 'cancel'])->name('entries.cancel');

    // Relatórios Financeiros e Exportação
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');

    // Perfil do Usuário
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
