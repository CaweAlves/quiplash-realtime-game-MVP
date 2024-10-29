<div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
        <h1 class="text-3xl font-bold mb-6 text-center text-indigo-600">Quiplash Game</h1>
        <p class="text-lg mb-4 text-center">Game Code: <span class="font-bold text-indigo-600">{{ $gameCode }}</span></p>
        
        @if ($errorMessage)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ $errorMessage }}</span>
            </div>
        @endif

        @if ($game->status === \App\Enums\GameStatus::Prompts && $currentPrompt)
            <h2 class="text-2xl font-semibold mb-4">Answer the prompt:</h2>
            <p class="text-xl mb-4 p-4 bg-indigo-100 rounded-md">{{ $currentPrompt }}</p>
            <form wire:submit.prevent="submitAnswer" class="space-y-4">
                <input type="text" wire:model="answer" placeholder="Your answer" class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition duration-300">Submit Answer</button>
            </form>
        @elseif ($game->status === \App\Enums\GameStatus::Voting && $currentPrompt)
            <h2 class="text-2xl font-semibold mb-4">Vote for the best answer:</h2>
            <p class="text-xl mb-4 p-4 bg-indigo-100 rounded-md">{{ $currentPrompt }}</p>
            <div class="space-y-4">
                @foreach ($game->answers[$currentPrompt] as $player => $answer)
                    @if ($player !== $playerName)
                        <button wire:click="submitVote('{{ $player }}')" class="w-full bg-white border-2 border-indigo-600 text-indigo-600 py-2 px-4 rounded-md hover:bg-indigo-100 transition duration-300">{{ $answer }}</button>
                    @endif
                @endforeach
            </div>
        @elseif ($game->status === \App\Enums\GameStatus::Results)
            <h2 class="text-2xl font-semibold mb-4">Results:</h2>
            @foreach ($game->prompts as $prompt)
                <div class="mb-6 p-4 bg-gray-100 rounded-md">
                    <h3 class="text-xl font-semibold mb-2">{{ $prompt }}</h3>
                    @foreach ($game->answers[$prompt] as $player => $answer)
                        <p class="mb-2">
                            <span class="font-semibold">{{ $player }}:</span> {{ $answer }} 
                            <span class="ml-2 px-2 py-1 bg-indigo-600 text-white rounded-full text-sm">Votes: {{ count($game->votes[$prompt][$player] ?? []) }}</span>
                        </p>
                    @endforeach
                </div>
            @endforeach
            <button wire:click="$refresh" class="w-full bg-green-500 text-white py-2 rounded-md hover:bg-green-600 transition duration-300 mt-4">Play Again</button>
        @else
            <p class="text-center text-gray-600">Waiting for other players...</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Echo.channel('game.{{ $gameCode }}')
            .listen('GameStateChanged', (e) => {
                Livewire.dispatch('refreshGame');
            });
    });
</script>
@endpush