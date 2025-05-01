<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function getRecommendation(Request $request): JsonResponse
    {
        // Validate request
        $validated = $request->validate([
            'crop_type' => 'required|in:wheat,rice,maize,cotton,pulses',
            'soil_ph' => 'required|numeric|min:4|max:9',
            'nitrogen' => 'required|numeric|min:0',
            'phosphorus' => 'required|numeric|min:0',
            'potassium' => 'required|numeric|min:0',
            'rainfall' => 'required|numeric|min:0',
            'temperature' => 'required|numeric|min:-10|max:50',
        ]);

        // Construct prompt for Gemini API, including growth prediction data
        $prompt = sprintf(
            "You are an agricultural expert. Analyze soil, crop, and weather data and provide fertilizer recommendations and crop growth predictions in JSON format with keys: fertilizer_type, quantity (kg/ha), sustainability_tips, growth_stages (array of objects with name and duration in days), estimated_yield (tons/ha). Input: Soil pH %.1f, nitrogen %d ppm, phosphorus %d ppm, potassium %d ppm, crop type %s, rainfall %d mm, temperature %d°C.",
            $validated['soil_ph'],
            $validated['nitrogen'],
            $validated['phosphorus'],
            $validated['potassium'],
            $validated['crop_type'],
            $validated['rainfall'],
            $validated['temperature']
        );

        try {
            // Get API key from environment
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                Log::error('Gemini API key is missing in .env');
                return response()->json(['error' => 'Gemini API key is not configured'], 500);
            }

            // Call Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json'
                ]
            ]);

            // Log the full response for debugging
            Log::debug('Gemini API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $data['candidates'][0]['content']['parts'][0]['text'];
                    $recommendation = json_decode($content, true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        // Provide fallback data if growth_stages or estimated_yield are missing
                        $recommendation['growth_stages'] = $recommendation['growth_stages'] ?? [
                            ['name' => 'Germination', 'duration' => '7 days'],
                            ['name' => 'Vegetative', 'duration' => '30 days'],
                            ['name' => 'Flowering', 'duration' => '20 days'],
                            ['name' => 'Maturity', 'duration' => '30 days']
                        ];
                        $recommendation['estimated_yield'] = $recommendation['estimated_yield'] ?? '4.5 tons/ha';

                        Log::info('Final Recommendation Data', ['recommendation' => $recommendation]);

                        return response()->json($recommendation, 200);
                    } else {
                        Log::error('Failed to parse Gemini response content as JSON', ['content' => $content]);
                        return response()->json(['error' => 'Invalid JSON response from Gemini'], 500);
                    }
                } else {
                    Log::error('Unexpected Gemini response structure', ['response' => $data]);
                    return response()->json(['error' => 'Unexpected response structure from Gemini'], 500);
                }
            } else {
                $status = $response->status();
                $errorMessage = match ($status) {
                    401 => 'Invalid Gemini API key',
                    429 => 'Gemini rate limit or quota exceeded. Check your plan at https://aistudio.google.com/.',
                    400 => 'Bad request to Gemini API',
                    default => 'Gemini API request failed with status ' . $status,
                };
                Log::error($errorMessage, ['response' => $response->body()]);
                return response()->json(['error' => $errorMessage], $status);
            }
        } catch (\Exception $e) {
            Log::error('Exception during Gemini API call', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error connecting to Gemini API: ' . $e->getMessage()], 500);
        }
    }
}