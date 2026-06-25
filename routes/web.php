<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/survey/{token}', function ($token) {
    return view('survey', ['token' => $token]);
})->name('survey.show');

Route::get('/print-saw', [ReportController::class, 'print'])->name('report.print')->middleware('auth');
Route::get('/print-flyer', [ReportController::class, 'printFlyer'])->name('report.flyer')->middleware('auth');
