<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GiphyService {

    protected $api_key;

    public function __construct() {
        $this->api_key = env('GIPHY_API_KEY');
    }

    public function searchGifs($query, $limit = 25, $offset = 0) {
        $response = Http::get('https://api.giphy.com/v1/gifs/search', [
            'api_key' => $this->api_key,
            'q' => $query,
            'limit' => $limit,
            'offset' => $offset
        ]);

        return $response->json();
    }

    public function getGifById($id) {
        $response = Http::get("https://api.giphy.com/v1/gifs/{$id}", [
            'api_key' => $this->api_key
        ]);

        return $response->json();
    }
}
