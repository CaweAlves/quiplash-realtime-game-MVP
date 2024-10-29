<?php

namespace App\Models;

use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'status', 'players', 'prompts', 'answers', 'votes'];

    protected $casts = [
        'status' => GameStatus::class,
        'players' => 'array',
        'prompts' => 'array',
        'answers' => 'array',
        'votes' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($game) {
            $game->prompts = $game->getRandomPrompts(10);
            $game->status = GameStatus::Waiting;
        });
    }

    public function getRandomPrompts($count): array
    {
        $allPrompts = [
            "What's the worst thing to say on a first date?",
            "What's the most useless superpower?",
            "What's the strangest thing you've ever eaten?",
            "What's the worst excuse for being late?",
            "What's the most ridiculous fashion trend you've ever seen?",
            "What's the weirdest thing you've seen in someone else's home?",
            "What's the most embarrassing thing you've done in public?",
            "What's the worst piece of advice you've ever received?",
            "What's the strangest thing you've used as a bookmark?",
            "What's the most unusual pet you can imagine?",
            "If you could make any food calorie-free, what would it be?",
            "What's the worst movie sequel ever made?",
            "What's the most useless invention you've ever seen?",
            "If you could rename yourself, what name would you choose?",
            "What's the weirdest dream you've ever had?",
        ];

        return collect($allPrompts)->random($count)->values()->toArray();
    }
}
