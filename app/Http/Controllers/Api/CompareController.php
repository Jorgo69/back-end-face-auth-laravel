<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompareImagesRequest;
use App\Services\FaceAuthService;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    use ApiResponse;

    public function __construct(
        private FaceAuthService $faceAuthService,
        private ImageService $imageService
    ) {}

    /**
     * Comparer deux images
     */
    public function compare(CompareImagesRequest $request): JsonResponse
    {
        // Valider les images
        $image1Validation = $this->imageService->validate($request->file('image1'));
        $image2Validation = $this->imageService->validate($request->file('image2'));

        if (!$image1Validation['valid']) {
            return $this->validationError(['image1' => $image1Validation['errors']]);
        }

        if (!$image2Validation['valid']) {
            return $this->validationError(['image2' => $image2Validation['errors']]);
        }

        // Comparer
        $result = $this->faceAuthService->compareTwoImages(
            $request->file('image1'),
            $request->file('image2')
        );

        if (!$result['success']) {
            return $this->error($result['error'], 400);
        }

        return $this->success([
            'match_found' => $result['match_found'],
            'detail' => $result['detail'],
            'response_time_ms' => $result['response_time_ms'],
            'similarity' => $result['match_found'] ? 'high' : 'low',
        ], 'Comparaison effectuée');
    }

    /**
     * Extraire les features d'une image (pour debug/test)
     */
    public function extract(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        $result = $this->faceAuthService->extractFeatures($request->file('image'));

        if (!$result['success']) {
            return $this->error($result['error'], 400);
        }

        return $this->success([
            'age' => $result['age'],
            'gender' => $result['gender'],
            'embedding_length' => count($result['embedding']),
            'response_time_ms' => $result['response_time_ms'],
        ], 'Features extraites avec succès');
    }

    /**
     * Vérifier la santé de l'API Python
     */
    public function health(): JsonResponse
    {
        $health = $this->faceAuthService->checkHealth();

        if ($health['status'] === 'online') {
            return $this->success($health, 'API Python opérationnelle');
        }

        return $this->error('API Python hors ligne', 503, $health);
    }
}