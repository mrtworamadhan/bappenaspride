<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\CheckinController;

Route::get('/', [ParticipantController::class, 'create'])->name('participant.create');
Route::post('/register', [ParticipantController::class, 'store'])->name('participant.store');
Route::get('/participant/{uuid}', [ParticipantController::class, 'show'])->name('participant.show');

Route::get('/gatekeeper/scan', [CheckinController::class, 'index'])->name('checkin.index');
Route::post('/gatekeeper/process', [CheckinController::class, 'store'])->name('checkin.store');