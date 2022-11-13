<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\PokemonController;
use Illuminate\Http\Request;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::with('userPokemon')->paginate(3);

        return view('home', compact('users'));
    }
}
