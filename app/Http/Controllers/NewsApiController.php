<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsApiController extends Controller
{
    public function HeadLines()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.newsapi.key'),
            ])->get('https://newsapi.org/v2/top-headlines', [
                'country' => 'pt', 
                'category' => 'technology',
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json(['error' => 'Erro na API'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }
    
}
