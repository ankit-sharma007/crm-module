<?php

use App\Http\Controllers\LeadController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/leads', [LeadController::class, 'index'])->name('api.leads.index');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('api.leads.show');
    Route::post('/leads', [LeadController::class, 'store'])->name('api.leads.store');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('api.leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('api.leads.destroy');
    Route::post('/leads/{lead}/assign', [LeadController::class, 'assign'])->name('api.leads.assign');
    Route::post('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('api.leads.status');
    Route::post('/leads/{lead}/note', [LeadController::class, 'addNote'])->name('api.leads.note');
    Route::get('/leads/activities', [LeadController::class, 'activities'])->name('api.leads.activities');
});
