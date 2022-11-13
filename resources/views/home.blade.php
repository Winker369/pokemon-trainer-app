@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex w-100 justify-content-between">
                <h1>Trainers</h1>
                {{ $users->links() }}
            </div>
            <div class="list-group">
                @forelse ($users as $user)
                <a href="#" class="list-group-item list-group-item-action bg-color" aria-current="true">
                    <div class="d-flex w-100 justify-content-between">
                        <h3>{{ $user->username }}</h3>
                    </div>
                    <div class="d-flex w-100 justify-content-between">
                        <div>
                            <h5>Favorite Pokemon:</h5>
                            @forelse ($user->toArray()['user_pokemon'] as $userPokemon)
                                @if ($userPokemon['favorite'])
                                    <p>#{{ $userPokemon['pokemon_id'] }} {{ $userPokemon['pokemon_name'] }}</p>
                                @endif
                            @empty
                                <p>No favorite pokemon yet.</p>
                            @endforelse
                        </div>
                        <div>
                            <h5>Liked Pokemons:</h5>
                            @forelse ($user->toArray()['user_pokemon'] as $userPokemon)
                                @if ($userPokemon['reaction'] == App\Enums\Reaction::LIKE->value)
                                    <p>#{{ $userPokemon['pokemon_id'] }} {{ $userPokemon['pokemon_name'] }}</p>
                                @endif
                            @empty
                                <p>No liked pokemons yet.</p>
                            @endforelse
                        </div>
                        <div>
                            <h5>Hated Pokemons:</h5>
                            @forelse ($user->toArray()['user_pokemon'] as $userPokemon)
                                @if ($userPokemon['reaction'] == App\Enums\Reaction::HATE->value)
                                    <p>#{{ $userPokemon['pokemon_id'] }} {{ $userPokemon['pokemon_name'] }}</p>
                                @endif
                            @empty
                                <p>No hated pokemons yet.</p>
                            @endforelse
                        </div>
                    </div>
                </a>
                @empty
                <p>No trainers to display.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
