<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Events\GameStateChanged;
use App\Enums\GameStatus;

class GamePlay extends Component
{
    public $gameCode;
    public $game;
    public $currentPrompt;
    public $answer = '';
    public $playerName;
    public $errorMessage = '';

    public function mount($gameCode)
    {
        $this->gameCode = $gameCode;
        $this->game = Game::where('code', $gameCode)->firstOrFail();
        $this->playerName = session('player_name');
        $this->currentPrompt = $this->getCurrentPromptForPlayer();
    }

    public function getCurrentPromptForPlayer(): ?string
    {
        $answeredPrompts = $this->game->answers[$this->playerName] ?? [];
        return collect($this->game->prompts)->diff(array_keys($answeredPrompts))->first();
    }

    public function submitAnswer()
    {
        $this->validate([
            'answer' => 'required|min:1|max:100',
        ]);

        $answers = $this->game->answers ?? [];
        $answers[$this->playerName][$this->currentPrompt] = $this->answer;
        $this->game->update(['answers' => $answers]);

        $this->answer = '';
        $this->currentPrompt = $this->getCurrentPromptForPlayer();

        if ($this->allPlayersAnswered()) {
            $this->game->update(['status' => GameStatus::Voting]);
        }

        event(new GameStateChanged($this->game));
    }

    public function submitVote($answerId)
    {
        $votes = $this->game->votes ?? [];
        $votes[$this->currentPrompt][$this->playerName] = $answerId;
        $this->game->update(['votes' => $votes]);

        $this->currentPrompt = $this->getNextPromptForVoting();

        if ($this->allPlayersVoted()) {
            $this->game->update(['status' => GameStatus::Results]);
        }

        event(new GameStateChanged($this->game));
    }

    private function getNextPromptForVoting(): ?string
    {
        $votedPrompts = array_keys($this->game->votes ?? []);
        return collect($this->game->prompts)->diff($votedPrompts)->first();
    }

    private function allPlayersAnswered(): bool
    {
        foreach ($this->game->players as $player) {
            if (count($this->game->answers[$player] ?? []) < count($this->game->prompts)) {
                return false;
            }
        }
        return true;
    }

    private function allPlayersVoted(): bool
    {
        foreach ($this->game->prompts as $prompt) {
            if (count($this->game->votes[$prompt] ?? []) < count($this->game->players)) {
                return false;
            }
        }
        return true;
    }

    public function render()
    {
        return view('livewire.game-play');
    }
}