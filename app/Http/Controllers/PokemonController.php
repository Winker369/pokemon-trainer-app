<?php

namespace App\Http\Controllers;

use App\Enums\Reaction;
use App\Models\User;
use App\Models\UserPokemon;
use Illuminate\Http\Request;
use PokePHP\PokeApi;

class PokemonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get users pokemons like and hate
        $userPokemons = User::find(auth()->user()->id)->userPokemon()->get();
        $pokeApi = new PokeApi;
        $resourceList = json_decode($pokeApi->resourceList('pokemon', 10, 0), true);
        $pokemons = collect($resourceList['results']);
        $results = $pokemons->map(function ($pokemon, $pokemonKey) use ($userPokemons) {
            // Search pokemon
            $userPokemon = $userPokemons->first(function ($userPokemon, $userPokemonKey) use ($pokemon) {
                return $pokemon['name'] == $userPokemon->pokemon_name;
            });
            $pokemon['id'] = $this->getPokemonId($pokemon['url']);
            $pokemon['favorite'] = false;
            $pokemon['reaction'] = '';

            if ($userPokemon) {
                $pokemon['favorite'] = $userPokemon->favorite;
                $pokemon['reaction'] = $userPokemon->reaction;
            }

            return $pokemon;
        })
        ->toArray();
        // Update with reaction key
        $resourceList['results'] = $results;
        // Add like and hate pokemon as a list
        $likeAndHatePokemons = $this->getLikeAndHate($userPokemons);
        $resourceList['liked_pokemons'] = $likeAndHatePokemons['liked_pokemons'];
        $resourceList['hated_pokemons'] = $likeAndHatePokemons['hated_pokemons'];

        return view('pokemon.index', compact('resourceList'));
    }

    /**
     * Favorite the pokemon
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function favorite(Request $request)
    {
        $response = [
            'status_code' => 400,
            'body' => [
                'message' =>  'Unable to add pokemon as favorite.'
            ]
        ];
        // Get pokemon id from url
        $pokemonId = $this->getPokemonId($request->url);
        // Validate if pokemon already has a favorite pokemon
        $favoritePokemon = UserPokemon::firstWhere([
            'user_id' => auth()->user()->id,
            'favorite' => true
        ]);

        if (!$favoritePokemon) {
            // Check if already has data
            $userPokemon = UserPokemon::where([
                'user_id' => auth()->user()->id,
                'pokemon_id' => $pokemonId
            ]);

            // Check if data is already created
            if ($userPokemon->get()->isNotEmpty()) {
                $userPokemon = $userPokemon->get()->first();
                // Update to favorite
                $userPokemon->favorite = true;
                $userPokemon->save();
            } else {
                // Create user_pokemon
                $userPokemon = UserPokemon::create([
                    'user_id' => auth()->user()->id,
                    'pokemon_id' => $pokemonId,
                    'pokemon_name' => $request->name,
                    'favorite' => true
                ]);
            }
            // Get like and hate pokemon
            $likeAndHatePokemons = $this->getLikeAndHate(User::find(auth()->user()->id)->userPokemon()->get());

            $response = [
                'status_code' => 200,
                'body' => [
                    'message' => 'Pokemon succesfully added as favorite.',
                    'data' => [
                        'favorite' => $userPokemon->favorite,
                        'reaction' => $userPokemon->reaction,
                        'liked_pokemons' => $likeAndHatePokemons['liked_pokemons'],
                        'hated_pokemons' => $likeAndHatePokemons['hated_pokemons']
                    ]
                ]
            ];
        } else {
            // Check if new favorite pokemon
            if ($favoritePokemon->pokemon_id !== $pokemonId) {
                // Update previous favorite pokemon
                if ($favoritePokemon->reaction !== null) {
                    $favoritePokemon->favorite = false;
                    $favoritePokemon->save();
                } else {
                    $favoritePokemon->delete();
                }
                // Check if new favorite pokemon has already data
                $userPokemon = UserPokemon::where([
                    'user_id' => auth()->user()->id,
                    'pokemon_id' => $pokemonId
                ]);

                // Check if data is already created
                if ($userPokemon->get()->isNotEmpty()) {
                    $userPokemon = $userPokemon->get()->first();
                    // Update to favorite
                    $userPokemon->favorite = true;
                    $userPokemon->save();
                } else {
                    // Create user_pokemon
                    $userPokemon = UserPokemon::create([
                        'user_id' => auth()->user()->id,
                        'pokemon_id' => $pokemonId,
                        'pokemon_name' => $request->name,
                        'favorite' => true
                    ]);
                }
                // Get like and hate pokemon
                $likeAndHatePokemons = $this->getLikeAndHate(User::find(auth()->user()->id)->userPokemon()->get());

                $response = [
                    'status_code' => 200,
                    'body' => [
                        'message' => 'Pokemon succesfully added as favorite.',
                        'data' => [
                            'favorite' => $userPokemon->favorite,
                            'reaction' => $userPokemon->reaction,
                            'liked_pokemons' => $likeAndHatePokemons['liked_pokemons'],
                            'hated_pokemons' => $likeAndHatePokemons['hated_pokemons']
                        ]
                    ]
                ];
            }
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * Unfavorite the pokemon
     *
     * @param  int  $pokemonId pokemon id
     * @return \Illuminate\Http\Response
     */
    public function unfavorite($pokemonId)
    {
        $response = [
            'status_code' => 400,
            'body' => [
                'message' =>  'Unable to remove pokemon as favorite.'
            ]
        ];
        // Find user_pokemon
        $userPokemon = UserPokemon::firstWhere([
            'user_id' => auth()->user()->id,
            'pokemon_id' => $pokemonId,
            'favorite' => true
        ]);

        if ($userPokemon) {
             // Check if pokemon is favorite
            if ($userPokemon->reaction !== null) {
                // Update to null
                $userPokemon->favorite = false;
                $userPokemon->save();
            } else {
                // Delete pokemon
                $userPokemon->delete();
            }
            // Get like and hate pokemon
            $userPokemons = UserPokemon::where([
                'user_id' => auth()->user()->id
            ]);
            $likeAndHatePokemons = $this->getLikeAndHate($userPokemons->get());
            $response = [
                'status_code' => 200,
                'body' => [
                    'message' => 'Pokemon succesfully removed as favorite.',
                    'data' => [
                        'favorite' => 0,
                        'reaction' => $userPokemon->reaction,
                        'liked_pokemons' => $likeAndHatePokemons['liked_pokemons'],
                        'hated_pokemons' => $likeAndHatePokemons['hated_pokemons']
                    ]
                ]
            ];
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * Like the pokemon
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function like(Request $request)
    {
        $response = [
            'status_code' => 400,
            'body' => [
                'message' =>  'Unable to like pokemon.'
            ]
        ];
        // Get pokemon id from url
        $pokemonId = $this->getPokemonId($request->url);
        // Validate if pokemon already liked or has already 3 liked pokemon
        $userPokemons = UserPokemon::where([
            'user_id' => auth()->user()->id
        ])
        ->wherein('reaction', Reaction::values());
        $likedPokemons = $userPokemons->get()->filter(function ($userPokemon, $userPokemonKey) {
            return $userPokemon->reaction == Reaction::LIKE->value;
        });

        if ($likedPokemons->count() < 3) {
            // Check if already has hate data
            $userPokemon = UserPokemon::where([
                'user_id' => auth()->user()->id,
                'pokemon_id' => $pokemonId
            ]);

            // Check if data is already created
            if ($userPokemon->get()->isNotEmpty()) {
                $userPokemon = $userPokemon->get()->first();
                // Update to like
                $userPokemon->reaction = Reaction::LIKE;
                $userPokemon->save();
            } else {
                // Create user_pokemon
                $userPokemon = UserPokemon::create([
                    'user_id' => auth()->user()->id,
                    'pokemon_id' => $pokemonId,
                    'pokemon_name' => $request->name,
                    'reaction' => Reaction::LIKE
                ]);
            }
            // Get like and hate pokemon
            $likeAndHatePokemons = $this->getLikeAndHate($userPokemons->get());

            $response = [
                'status_code' => 200,
                'body' => [
                    'message' => 'Pokemon succesfully liked.',
                    'data' => [
                        'reaction' => Reaction::LIKE,
                        'liked_pokemons' => $likeAndHatePokemons['liked_pokemons'],
                        'hated_pokemons' => $likeAndHatePokemons['hated_pokemons']
                    ]
                ]
            ];
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * Unlike the pokemon
     *
     * @param  int  $pokemonId pokemon id
     * @return \Illuminate\Http\Response
     */
    public function unlike($pokemonId)
    {
        $response = [
            'status_code' => 400,
            'body' => [
                'message' =>  'Unable to unlike pokemon.'
            ]
        ];
        // Find user_pokemon
        $userPokemon = UserPokemon::firstWhere([
            'user_id' => auth()->user()->id,
            'pokemon_id' => $pokemonId,
            'reaction' => Reaction::LIKE
        ]);

        if ($userPokemon) {
             // Check if pokemon is favorite
            if ($userPokemon->favorite) {
                // Update to null
                $userPokemon->reaction = null;
                $userPokemon->save();
            } else {
                // Delete pokemon
                $userPokemon->delete();
            }
            // Get like and hate pokemon
            $userPokemons = UserPokemon::where([
                'user_id' => auth()->user()->id
            ])
            ->wherein('reaction', Reaction::values());
            $likeAndHatePokemons = $this->getLikeAndHate($userPokemons->get());
            $response = [
                'status_code' => 200,
                'body' => [
                    'message' => 'Pokemon succesfully unliked.',
                    'data' => [
                        'reaction' => '',
                        'liked_pokemons' => $likeAndHatePokemons['liked_pokemons'],
                        'hated_pokemons' => $likeAndHatePokemons['hated_pokemons']
                    ]
                ]
            ];
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * Hate the pokemon
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function hate(Request $request)
    {
        $response = [
            'status_code' => 400,
            'body' => [
                'message' =>  'Unable to hate pokemon.'
            ]
        ];
        // Get pokemon id from url
        $pokemonId = $this->getPokemonId($request->url);
        // Validate if pokemon already hated or has already 3 hated pokemon
        $userPokemons = UserPokemon::where([
            'user_id' => auth()->user()->id
        ])
        ->wherein('reaction', Reaction::values());
        $hatedPokemons = $userPokemons->get()->filter(function ($userPokemon, $userPokemonKey) {
            return $userPokemon->reaction == Reaction::HATE->value;
        });

        if ($hatedPokemons->count() < 3) {
            // Check if already has hate data
            $userPokemon = UserPokemon::where([
                'user_id' => auth()->user()->id,
                'pokemon_id' => $pokemonId
            ])
            ->wherein('reaction', Reaction::values());

            // Check if data is already created
            if ($userPokemon->get()->isNotEmpty()) {
                $userPokemon = $userPokemon->get()->first();

                // Check if data is hate
                if ($userPokemon->reaction == Reaction::LIKE->value) {
                    // Update to like
                    $userPokemon->reaction = Reaction::HATE;
                    $userPokemon->save();
                }
            } else {
                // Create user_pokemon
                $userPokemon = UserPokemon::create([
                    'user_id' => auth()->user()->id,
                    'pokemon_id' => $pokemonId,
                    'pokemon_name' => $request->name,
                    'reaction' => Reaction::HATE
                ]);
            }
            // Get like and hate pokemon
            $likeAndHatePokemons = $this->getLikeAndHate($userPokemons->get());

            $response = [
                'status_code' => 200,
                'body' => [
                    'message' => 'Pokemon succesfully hated.',
                    'data' => [
                        'reaction' => Reaction::HATE,
                        'liked_pokemons' => $likeAndHatePokemons['liked_pokemons'],
                        'hated_pokemons' => $likeAndHatePokemons['hated_pokemons']
                    ]
                ]
            ];
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * Unhate the pokemon
     *
     * @param  int  $pokemonId pokemon id
     * @return \Illuminate\Http\Response
     */
    public function unhate($pokemonId)
    {
        $response = [
            'status_code' => 400,
            'body' => [
                'message' =>  'Unable to unhate pokemon.'
            ]
        ];
        // Find user_pokemon
        $userPokemon = UserPokemon::firstWhere([
            'user_id' => auth()->user()->id,
            'pokemon_id' => $pokemonId,
            'reaction' => Reaction::HATE
        ]);

        if ($userPokemon) {
            // Check if pokemon is favorite
            if ($userPokemon->favorite) {
                // Update to null
                $userPokemon->reaction = null;
                $userPokemon->save();
            } else {
                // Delete pokemon
                $userPokemon->delete();
            }
            // Get like and hate pokemon
            $userPokemons = UserPokemon::where([
                'user_id' => auth()->user()->id
            ])
            ->wherein('reaction', Reaction::values());
            $likeAndHatePokemons = $this->getLikeAndHate($userPokemons->get());
            $response = [
                'status_code' => 200,
                'body' => [
                    'message' => 'Pokemon succesfully unhate.',
                    'data' => [
                        'reaction' => '',
                        'liked_pokemons' => $likeAndHatePokemons['liked_pokemons'],
                        'hated_pokemons' => $likeAndHatePokemons['hated_pokemons']
                    ]
                ]
            ];
        }

        return response()->json($response, $response['status_code']);
    }

    /**
     * getLikeAndHate
     *
     * @param  object  $userPokemons collection user_pokemon
     * @return array Array of liked and hated pokemons
     */
    protected function getLikeAndHate($userPokemons)
    {
        $likeAndHatePokemons = [];
        $likeAndHatePokemons['liked_pokemons'] = $userPokemons->filter(function ($userPokemon, $userPokemonKey) {
            return $userPokemon->reaction == Reaction::LIKE->value;
        })
        ->map(function ($pokemon, $pokemonKey) {
            $modifiedPokemon = [
                'id' => $pokemon->pokemon_id,
                'name' => $pokemon->pokemon_name,
                'url' => config('app.pokemon_api_url') . $pokemon->pokemon_id . '/',
                'favorite' => $pokemon->favorite,
                'reaction' => $pokemon->reaction
            ];

            return $modifiedPokemon;
        })
        ->toArray();
        $likeAndHatePokemons['hated_pokemons']  = $userPokemons->filter(function ($userPokemon, $userPokemonKey) {
            return $userPokemon->reaction == Reaction::HATE->value;
        })
        ->map(function ($pokemon, $pokemonKey) {
            $modifiedPokemon = [
                'id' => $pokemon->pokemon_id,
                'name' => $pokemon->pokemon_name,
                'url' => config('app.pokemon_api_url') . $pokemon->pokemon_id . '/',
                'favorite' => $pokemon->favorite,
                'reaction' => $pokemon->reaction
            ];

            return $modifiedPokemon;
        })
        ->toArray();

        return $likeAndHatePokemons;
    }

    /**
     * getPokemonId
     *
     * @param  object  $userPokemons collection user_pokemon
     * @return int Pokemon ID
     */
    protected function getPokemonId($url)
    {
        $url = explode('/', $url);
        $pokemonId = $url[count($url) - 2];

        return $pokemonId;
    }
}
