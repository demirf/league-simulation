<?php

use App\Http\Controllers\TeamController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\MatchesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [TeamController::class, 'index'])->name('home');
Route::post('tournaments', [TournamentController::class, 'store'])->name('tournaments.store');
Route::get('tournaments/{tournamentId}/matches', [MatchesController::class, 'getAll'])->name('matches.getAll');


