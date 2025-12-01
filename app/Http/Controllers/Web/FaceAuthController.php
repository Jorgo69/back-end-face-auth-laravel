<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterFaceRequest;
use App\Http\Requests\VerifyFaceRequest;
use App\Services\FaceAuthService;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FaceAuthController extends Controller
{
    public function __construct(
        private FaceAuthService $faceAuthService,
        private ImageService $imageService
    ) {
        $this->middleware('auth')->except(['showLoginForm', 'loginWithFace']);
    }

    /**
     * Afficher le formulaire d'enregistrement du visage
     */
    public function showRegisterForm(): View
    {
        $user = Auth::user();

        if ($user->hasFaceRegistered()) {
            return view('face-auth.already-registered', compact('user'));
        }

        return view('face-auth.register');
    }

    /**
     * Enregistrer le visage de l'utilisateur
     */
    public function register(RegisterFaceRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur n'a pas déjà enregistré son visage
        if ($user->hasFaceRegistered()) {
            return redirect()
                ->route('dashboard')
                ->with('info', 'Vous avez déjà enregistré votre visage.');
        }

        // Valider l'image
        $validation = $this->imageService->validate($request->file('image'));
        
        if (!$validation['valid']) {
            return back()
                ->withErrors(['image' => implode(' ', $validation['errors'])]);
        }

        // Générer le token via l'API Python
        $result = $this->faceAuthService->tokenize($request->file('image'));

        if (!$result['success']) {
            return back()
                ->withErrors(['image' => $result['error']])
                ->with('error', 'Échec de l\'enregistrement : ' . $result['error']);
        }

        // Mettre à jour l'utilisateur
        $user->update([
            'face_token' => $result['token'],
            'face_age' => $result['age'],
            'face_gender' => $result['gender'],
            'face_registered_at' => now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', '✅ Votre visage a été enregistré avec succès !');
    }

    /**
     * Afficher le formulaire de connexion par visage
     */
    public function showLoginForm(): View
    {
        return view('face-auth.login');
    }

    /**
     * Connexion par reconnaissance faciale
     */
    public function loginWithFace(VerifyFaceRequest $request): RedirectResponse
    {
        $email = $request->input('email');
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user || !$user->hasFaceRegistered()) {
            return back()
                ->withErrors(['email' => 'Cet utilisateur n\'a pas enregistré de visage.'])
                ->with('error', 'Aucun visage enregistré pour cet email.');
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
            return back()
                ->withErrors(['image' => 'Erreur technique : ' . $result['error']])
                ->with('error', $result['error']);
        }

        if (!$result['match_found']) {
            return back()
                ->withErrors(['image' => 'Visage non reconnu.'])
                ->with('error', 'Votre visage ne correspond pas à celui enregistré.');
        }

        // Authentifier l'utilisateur
        Auth::login($user);
        $user->incrementFaceVerificationCount();

        return redirect()
            ->route('dashboard')
            ->with('success', '✅ Connexion réussie par reconnaissance faciale !');
    }

    /**
     * Réenregistrer le visage
     */
    public function reregisterForm(): View
    {
        return view('face-auth.reregister');
    }

    /**
     * Traiter le réenregistrement
     */
    public function reregister(RegisterFaceRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Valider l'image
        $validation = $this->imageService->validate($request->file('image'));
        
        if (!$validation['valid']) {
            return back()->withErrors(['image' => implode(' ', $validation['errors'])]);
        }

        // Générer le nouveau token
        $result = $this->faceAuthService->tokenize($request->file('image'));

        if (!$result['success']) {
            return back()
                ->withErrors(['image' => $result['error']])
                ->with('error', $result['error']);
        }

        // Mettre à jour
        $user->update([
            'face_token' => $result['token'],
            'face_age' => $result['age'],
            'face_gender' => $result['gender'],
            'face_registered_at' => now(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', '✅ Votre visage a été mis à jour avec succès !');
    }
}