<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center text-indigo-600">Quiplash Game Lobby</h1>
        <p class="text-lg mb-4 text-center">Game Code: <span class="font-bold text-indigo-600">{{ $gameCode }}</span></p>
        
        @if ($errorMessage)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ $errorMessage }}</span>
            </div>
        @endif

        @if (!$game->players || !in_array($playerName, $game->players ?? []))
            <form wire:submit.prevent="joinGame" class="space-y-4 mb-6">
                <input type="text" wire:model="playerName" placeholder="Enter your name" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition duration-300">Join Game</button>
            </form>
        @endif

        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Players:</h2>
            <ul class="space-y-2">
                @forelse ($game->players ?? [] as $player)
                    <li class="bg-gray-100 px-3 py-2 rounded-md">{{ $player }}</li>
                @empty
                    <li class="text-gray-500">No players have joined yet.</li>
                @endforelse
            </ul>
        </div>
        
        @if (count($game->players ?? []) >= 3 && in_array($playerName, $game->players ?? []))
            <button wire:click="startGame" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition duration-300">Start Game</button>
        @else
            <p class="text-center text-gray-600">Waiting for more players to join...</p>
        @endif
    </div>
</div>