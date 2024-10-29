<?php

namespace Database\Factories;

use App\Models\Game;
use App\Enums\GameStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->regexify('[A-Z0-9]{6}')),
            'status' => $this->faker->randomElement(GameStatus::cases()),
            'players' => $this->faker->randomElements(['Player1', 'Player2', 'Player3', 'Player4', 'Player5'], $this->faker->numberBetween(3, 5)),
            'prompts' => $this->faker->randomElements([
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
            ], 5),
            'answers' => function (array $attributes) {
                $answers = [];
                foreach ($attributes['players'] as $player) {
                    foreach ($attributes['prompts'] as $prompt) {
                        $answers[$player][$prompt] = $this->faker->sentence();
                    }
                }
                return $answers;
            },
            'votes' => function (array $attributes) {
                $votes = [];
                if ($attributes['status'] === GameStatus::Voting || $attributes['status'] === GameStatus::Results) {
                    foreach ($attributes['prompts'] as $prompt) {
                        foreach ($attributes['players'] as $voter) {
                            $votedFor = $this->faker->randomElement(array_diff($attributes['players'], [$voter]));
                            $votes[$prompt][$voter] = $votedFor;
                        }
                    }
                }
                return $votes;
            },
        ];
    }

    public function waiting(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => GameStatus::Waiting,
                'answers' => [],
                'votes' => [],
            ];
        });
    }

    public function inProgress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => GameStatus::Prompts,
            ];
        });
    }

    public function completed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => GameStatus::Results,
            ];
        });
    }
}