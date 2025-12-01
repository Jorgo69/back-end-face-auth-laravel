<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompareImagesRequest;
use App\Services\FaceAuthService;
use App\Services\ImageService;
use Illuminate\View\View;

class CompareController extends Controller
{
    public function __construct(
        private FaceAuthService $faceAuthService,
        private ImageService $imageService
    ) {}

    /**
     * Afficher le formulaire de comparaison
     */
    public function index(): View
    {
        return view('compare.index');
    }

    /**
     * Comparer deux images
     */
    public function compare(CompareImagesRequest $request)
    {
        // Valider les deux images
        $image1Validation = $this->imageService->validate($request->file('image1'));
        $image2Validation = $this->imageService->validate($request->file('image2'));

        if (!$image1Validation['valid']) {
            return back()->withErrors(['image1' => implode(' ', $image1Validation['errors'])]);
        }

        if (!$image2Validation['valid']) {
            return back()->withErrors(['image2' => implode(' ', $image2Validation['errors'])]);
        }

        // Comparer les deux images
        $result = $this->faceAuthService->compareTwoImages(
            $request->file('image1'),
            $request->file('image2')
        );

        if (!$result['success']) {
            return back()
                ->with('error', '❌ ' . $result['error'])
                ->withInput();
        }

        // Résultat
        $comparisonResult = [
            'match_found' => $result['match_found'],
            'detail' => $result['detail'],
            'response_time_ms' => $result['response_time_ms'],
        ];

        return view('compare.result', compact('comparisonResult'));
    }
}