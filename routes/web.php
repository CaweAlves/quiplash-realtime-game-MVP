<?php

use App\Livewire\GameLobby;
use Illuminate\Support\Facades\Route;

Route::get('/', GameLobby::class)->name('game.lobby');
