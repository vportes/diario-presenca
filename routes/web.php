<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\JustificationController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () { return redirect()->route('presence.index'); });
    Route::get('/dashboard', function () { return redirect()->route('presence.index'); })->name('dashboard');

    // Rotas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');

    // Histórico (rota própria) + acionar justificativa
    Route::get('/historico', [PresenceController::class, 'history'])->name('historico');
    Route::post('/historico/justify', [PresenceController::class, 'justify'])->name('historico.justify');

    Route::get('/presences', [PresenceController::class,'index'])->name('presence.index');
    Route::get('/presences/create', [PresenceController::class,'create'])->name('presence.create');
    // Presenças
    Route::get('/presences', [PresenceController::class,'index'])->name('presence.index');
    Route::get('/presences/create', [PresenceController::class,'create'])->name('presence.create');
    Route::post('/presences', [PresenceController::class,'store'])->name('presence.store');

    // Justificativas
    Route::get('/justifications/create/{presence?}', [JustificationController::class,'create'])->name('justifications.create');
    Route::post('/justifications', [JustificationController::class,'store'])->name('justifications.store');
    Route::get('/justifications/review/{id}', [JustificationController::class,'review'])->name('justifications.review');
    Route::post('/justifications/decide/{id}', [JustificationController::class,'decide'])->name('justifications.decide');

    // Observações e relatórios
    Route::post('/observations/{user}', [ObservationController::class,'store'])->name('observations.store');
    Route::get('/reports/presences/export', [ReportController::class,'exportCsv'])->name('reports.presences.export');
});

require __DIR__ . '/auth.php';
