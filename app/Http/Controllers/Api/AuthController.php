<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFaceRequest;
use App\Http\Requests\VerifyFaceRequest;
use App\Models\User;
use App\Services\FaceAuthService;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private FaceAuthService $faceAuthService,
        private ImageService $imageService
    ) {}

    /**
     * Inscription classique (email/password)
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Créer un token Sanctum (si installé)
        // $token = $user->createToken('api-token')->plainTextToken;

        return $this->created([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            // 'token' => $token,
        ], 'Compte créé avec succès');
    }

    /**
     * Connexion classique (email/password)
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        $user = Auth::user();
        
        // Créer un token Sanctum
        // $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'has_face_registered' => $user->hasFaceRegistered(),
            ],
            // 'token' => $token,
        ], 'Connexion réussie');
    }

    /**
     * Enregistrer le visage de l'utilisateur connecté
     */
    public function registerFace(RegisterFaceRequest $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->hasFaceRegistered()) {
            return $this->error('Vous avez déjà enregistré votre visage.', 400);
        }

        // Valider l'image
        $validation = $this->imageService->validate($request->file('image'));
        
        if (!$validation['valid']) {
            return $this->validationError(['image' => $validation['errors']]);
        }

        // Générer le token
        $result = $this->faceAuthService->tokenize($request->file('image'));

        if (!$result['success']) {
            return $this->error($result['error'], 400);
        }

        // Mettre à jour l'utilisateur
        $user->update([
            'face_token' => $result['token'],
            'face_age' => $result['age'],
            'face_gender' => $result['gender'],
            'face_registered_at' => now(),
        ]);

        return $this->success([
            'face_registered' => true,
            'face_age' => $result['age'],
            'face_gender' => $result['gender'],
        ], 'Visage enregistré avec succès');
    }

    /**
     * Connexion par reconnaissance faciale
     */
    public function loginWithFace(VerifyFaceRequest $request): JsonResponse
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user || !$user->hasFaceRegistered()) {
            return $this->error('Cet utilisateur n\'a pas enregistré de visage.', 404);
        }

        // Vérifier le visage
        $result = $this->faceAuthService->verify(
            $request->file('image'),
            $user->face_token,
            [
                'type' => \App\Models\FaceVerification::TYPE_USER_LOGIN,
                'user_id' => $user->id,
            ]
        );

        if (!$result['success']) {
            return $this->error($result['error'], 400);
        }

        if (!$result['match_found']) {
            return $this->error('Visage non reconnu.', 401);
        }

        // Authentifier
        Auth::login($user);
        $user->incrementFaceVerificationCount();

        // Créer un token Sanctum
        // $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            // 'token' => $token,
        ], 'Connexion réussie par reconnaissance faciale');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        
        // Révoquer le token Sanctum
        // $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Déconnexion réussie');
    }

    /**
     * Profil utilisateur
     */
    public function me(Request $request): JsonResponse
    {
        $user = Auth::user();

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'has_face_registered' => $user->hasFaceRegistered(),
            'face_registered_at' => $user->face_registered_at?->toISOString(),
            'face_verification_count' => $user->face_verification_count,
            'created_at' => $user->created_at->toISOString(),
        ]);
    }
}