<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Events\PlayerJoined;
use App\Enums\GameStatus;

class GameLobby extends Component
{
    public $gameCode;
    public $playerName = '';
    public $game;
    public $errorMessage = '';
    public $isHost = false;

    public function getListeners()
    {
        return [
            "echo:game.{$this->game->code}, PlayerJoined" => 'refreshLobby',
        ];
    }

    public function mount($gameCode = null)
    {
        $this->gameCode = $gameCode ?? $this->generateGameCode();
        $this->game = Game::firstOrCreate(
            ['code' => $this->gameCode],
            ['status' => GameStatus::Waiting, 'players' => []]
        );
        $this->isHost = !$gameCode; // The player who creates the game is the host
    }

    public function joinGame()
    {
        $this->validate([
            'playerName' => 'required|min:2|max:20',
        ]);

        $players = $this->game->players ?? [];
        if (in_array($this->playerName, $players)) {
            $this->errorMessage = 'This name is already taken. Please choose another.';
            return;
        }

        $players[] = $this->playerName;
        $this->game->update(['players' => $players]);

        event(new PlayerJoined($this->game, $this->playerName));

        session(['player_name' => $this->playerName]);
        $this->errorMessage = '';
    }

    public function startGame()
    {
        if (count($this->game->players ?? []) < 3) {
            $this->errorMessage = 'At least 3 players are required to start the game.';
            return;
        }

        $this->game->update(['status' => GameStatus::Prompts]);
        $this->redirect(route('game.play', ['gameCode' => $this->gameCode]));
    }

    public function refreshLobby()
    {
        $this->redirect(route('game.play', ['gameCode' => $this->gameCode]));

    }

    private function generateGameCode(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 6));
    }

    public function render()
    {
        return view('livewire.game-lobby');
    }
}