<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GiphyService;
use App\Models\FavoriteGif;
use Illuminate\Support\Facades\Auth;

class GifController extends Controller
{
    protected $giphyService;

    public function __construct(GiphyService $giphyService)
    {
        $this->giphyService = $giphyService;
    }

    public function searchGifs(Request $request) {

        try {
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
            } catch (\Exception $e) {
                
                return response()->json([
                    'message' => 'La consulta no puede estar vacía',
                    'error' => $e->getMessage()
                ], 500);
            }
    }

    public function getGifById($id) {

        try {
            $gif = $this->giphyService->getGifById($id);    
            return response()->json($gif, $gif["meta"]["status"]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar el GIF.',
                'error' => $e->getMessage()
            ], 500); 
        }
    }

    public function storeFavoriteGif(Request $request) {
        try {
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
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Ocurrió un error al agregar el GIF a favoritos.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
