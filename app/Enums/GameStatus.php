<?php

namespace App\Enums;

enum GameStatus: string
{
    case Waiting = 'waiting';
    case Prompts = 'prompts';
    case Voting = 'voting';
    case Results = 'results';
}