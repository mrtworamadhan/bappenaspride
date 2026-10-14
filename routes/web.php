<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\ReportController;

Route::get('/', [ParticipantController::class, 'create'])->name('participant.create');
Route::post('/register', [ParticipantController::class, 'store'])->name('participant.store');
Route::get('/participant/{uuid}', [ParticipantController::class, 'show'])->name('participant.show');

Route::get('/gatekeeper/scan', [CheckinController::class, 'index'])->name('checkin.index');
Route::post('/gatekeeper/process', [CheckinController::class, 'store'])->name('checkin.store');

Route::get('/admin/report/registered-pdf', [ReportController::class, 'downloadRegisteredPdf'])->name('report.registered.pdf');
Route::get('/admin/report/attendance-pdf', [ReportController::class, 'downloadAttendancePdf'])->name('report.attendance.pdf');