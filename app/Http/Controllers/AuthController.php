<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\FavoriteGif;
use Illuminate\Support\Facades\Hash;
use App\Services\GiphyService;

class AuthController extends Controller {

    protected $giphyService;

    public function __construct(GiphyService $giphyService)
    {
        $this->giphyService = $giphyService;
    }
    
    public function login(Request $request) {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Try to authenticate the user
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('YourAppName')->accessToken;

            return response()->json(['token' => $token]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function searchGifs(Request $request) {

        $request->validate([
            'query' => 'required|string',
            'limit' => 'nullable|integer',
            'offset' => 'nullable|integer',
        ]);

        $query = $request->input('query');
        $limit = $request->input('limit', 25);
        $offset = $request->input('offset', 0);

        $gifs = $this->giphyService->searchGifs($query, $limit, $offset);
        return response()->json($gifs);
    }

    public function getGifById($id) {

        $gif = $this->giphyService->getGifById($id);
        return response()->json($gif);
    }

    public function storeFavoriteGif(Request $request) {

        $request->validate([
            'gif_id' => 'required|string',
            'alias' => 'required|string',
        ]);

        $user = Auth::user();

        $existingFavorite = FavoriteGif::where('user_id', $user->id)
        ->where('gif_id', $request->input('gif_id'))
        ->first();

        if ($existingFavorite) {
            return response()->json(['message' => 'Este GIF ya existe en tus favoritos.'], 409);
        }

        $favoriteGif = FavoriteGif::create([
            'user_id' => $user->id,
            'gif_id' => $request->input('gif_id'),
            'alias' => $request->input('alias'),
        ]);

        return response()->json(['message' => 'GIF agregado a favoritos.'], 201);
    }
}
