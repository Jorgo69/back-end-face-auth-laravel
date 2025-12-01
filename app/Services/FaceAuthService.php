<?php

namespace App\Services;

use App\Models\FaceVerification;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceAuthService
{
    private string $apiUrl;
    private int $timeout;

    public function __construct()
    {
        // Charger depuis la BDD (ou fallback sur .env)
        $this->apiUrl = Setting::get('python_api_url', config('services.faceauth.url'));
        $this->timeout = Setting::get('python_api_timeout', config('services.faceauth.timeout'));
    }

    /**
     * Vérifier la santé de l'API Python
     */
    public function checkHealth(): array
    {
        try {
            $startTime = microtime(true);
            
            $response = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/health");
            
            $responseTime = (microtime(true) - $startTime) * 1000;

            if ($response->successful()) {
                return [
                    'status' => 'online',
                    'data' => $response->json(),
                    'response_time_ms' => round($responseTime, 2),
                ];
            }

            return [
                'status' => 'offline',
                'error' => 'API returned non-200 status',
                'response_time_ms' => round($responseTime, 2),
            ];

        } catch (\Exception $e) {
            Log::error('FaceAuth Health Check Failed', ['error' => $e->getMessage()]);
            
            return [
                'status' => 'offline',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Extraire les features d'un visage
     */
    public function extractFeatures(UploadedFile $image): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->attach('image', $image->get(), $image->getClientOriginalName())
                ->post("{$this->apiUrl}/face/extract");

            $responseTime = (microtime(true) - $startTime) * 1000;

            if (!$response->successful()) {
                throw new \Exception($this->extractErrorMessage($response));
            }

            $data = $response->json();

            return [
                'success' => true,
                'age' => $data['age'],
                'gender' => $data['gender'],
                'embedding' => $data['embedding'],
                'response_time_ms' => round($responseTime, 2),
            ];

        } catch (\Exception $e) {
            Log::error('FaceAuth Extract Failed', [
                'error' => $e->getMessage(),
                'image' => $image->getClientOriginalName(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /**
     * Générer un token sécurisé depuis un visage
     */
    public function tokenize(UploadedFile $image): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->attach('image', $image->get(), $image->getClientOriginalName())
                ->post("{$this->apiUrl}/face/tokenize");

            $responseTime = (microtime(true) - $startTime) * 1000;

            if (!$response->successful()) {
                throw new \Exception($this->extractErrorMessage($response));
            }

            $data = $response->json();

            return [
                'success' => true,
                'token' => $data['token'],
                'age' => $data['age'],
                'gender' => $data['gender'],
                'response_time_ms' => round($responseTime, 2),
            ];

        } catch (\Exception $e) {
            Log::error('FaceAuth Tokenize Failed', [
                'error' => $e->getMessage(),
                'image' => $image->getClientOriginalName(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /**
     * Vérifier la correspondance entre une image et un token
     */
    public function verify(UploadedFile $image, string $tokenReference, array $metadata = []): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)
                ->attach('image', $image->get(), $image->getClientOriginalName())
                ->post("{$this->apiUrl}/face/match", [
                    'token_reference' => $tokenReference
                ]);

            $responseTime = (microtime(true) - $startTime) * 1000;

            if (!$response->successful()) {
                // Enregistrer l'échec
                FaceVerification::createVerification([
                    'type' => $metadata['type'] ?? FaceVerification::TYPE_USER_LOGIN,
                    'user_id' => $metadata['user_id'] ?? null,
                    'person_id' => $metadata['person_id'] ?? null,
                    'match_found' => false,
                    'status' => FaceVerification::STATUS_ERROR,
                    'detail' => $this->extractErrorMessage($response),
                    'response_time_ms' => round($responseTime, 2),
                    'metadata' => $metadata,
                ]);

                throw new \Exception($this->extractErrorMessage($response));
            }

            $data = $response->json();
            $matchFound = $data['match_found'] ?? false;

            // Enregistrer la vérification
            FaceVerification::createVerification([
                'type' => $metadata['type'] ?? FaceVerification::TYPE_USER_LOGIN,
                'user_id' => $metadata['user_id'] ?? null,
                'person_id' => $metadata['person_id'] ?? null,
                'match_found' => $matchFound,
                'status' => $matchFound ? FaceVerification::STATUS_SUCCESS : FaceVerification::STATUS_FAILED,
                'detail' => $data['detail'] ?? null,
                'response_time_ms' => round($responseTime, 2),
                'metadata' => $metadata,
            ]);

            return [
                'success' => true,
                'match_found' => $matchFound,
                'detail' => $data['detail'] ?? '',
                'response_time_ms' => round($responseTime, 2),
            ];

        } catch (\Exception $e) {
            Log::error('FaceAuth Verify Failed', [
                'error' => $e->getMessage(),
                'metadata' => $metadata,
            ]);

            return [
                'success' => false,
                'match_found' => false,
                'error' => $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /**
     * Comparer deux images directement
     */
    public function compareTwoImages(UploadedFile $image1, UploadedFile $image2): array
    {
        // Tokeniser la première image
        $token1Result = $this->tokenize($image1);
        
        if (!$token1Result['success']) {
            return [
                'success' => false,
                'error' => 'Échec du traitement de la première image : ' . $token1Result['error'],
            ];
        }

        // Comparer la deuxième avec le token
        return $this->verify($image2, $token1Result['token'], [
            'type' => FaceVerification::TYPE_TWO_IMAGES,
        ]);
    }

    /**
     * Extraire le message d'erreur de la réponse Python
     */
    private function extractErrorMessage($response): string
    {
        $json = $response->json();

        // Format structuré (notre API)
        if (isset($json['detail']['error']['message'])) {
            $message = $json['detail']['error']['message'];
            if (isset($json['detail']['error']['details'])) {
                $message .= ' - ' . $json['detail']['error']['details'];
            }
            return $message;
        }

        // Format simple
        if (isset($json['detail'])) {
            return is_string($json['detail']) ? $json['detail'] : json_encode($json['detail']);
        }

        return 'Erreur inconnue de l\'API Python';
    }
}