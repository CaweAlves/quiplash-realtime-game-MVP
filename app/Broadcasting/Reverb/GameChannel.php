<?php

namespace App\Broadcasting\Reverb;

use App\Models\User;
use App\Models\Game;

class GameChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, Game $game): array|bool
    {
        $game = Game::where('code', $game)->first();
        return $game && in_array($user->name, $game->players ?? []);
    }
}