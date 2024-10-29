<?php

use App\Livewire\GameLobby;
use App\Livewire\GamePlay;
use Illuminate\Support\Facades\Route;

Route::get('/', GameLobby::class)->name('game.lobby');
Route::get('/game/{gameCode}', GamePlay::class)->name('game.play');