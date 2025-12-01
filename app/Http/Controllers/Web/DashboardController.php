<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FaceVerification;
use App\Models\Person;
use App\Services\FaceAuthService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private FaceAuthService $faceAuthService
    ) {
        // $this->middleware('auth');
    }

    

    /**
     * Afficher le dashboard principal
     */
    public function index()
    {
        $user = Auth::user();

        // Statistiques utilisateur
        $stats = [
            'has_face_registered' => $user->hasFaceRegistered(),
            'face_registered_at' => $user->face_registered_at,
            'verification_count' => $user->face_verification_count,
            'last_verified_at' => $user->face_last_verified_at,
            
            // Statistiques personnes
            'persons_count' => Person::where('user_id', $user->id)->count(),
            'persons_active' => Person::where('user_id', $user->id)->active()->count(),
            
            // Statistiques vérifications
            'verifications_today' => FaceVerification::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'verifications_success' => FaceVerification::where('user_id', $user->id)
                ->successful()
                ->count(),
        ];

        // Dernières vérifications
        $recentVerifications = FaceVerification::where('user_id', $user->id)
            ->with('person')
            ->latest()
            ->limit(10)
            ->get();

        // Vérifier la santé de l'API Python
        $pythonApiHealth = $this->faceAuthService->checkHealth();

        return view('dashboard', compact('stats', 'recentVerifications', 'pythonApiHealth'));
    }

    /**
     * Afficher les statistiques détaillées
     */
    public function statistics()
    {
        $user = Auth::user();

        // Vérifications par jour (7 derniers jours)
        $verificationsByDay = FaceVerification::where('user_id', $user->id)
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(match_found) as success')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Vérifications par type
        $verificationsByType = FaceVerification::where('user_id', $user->id)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        // Temps de réponse moyen
        $avgResponseTime = FaceVerification::where('user_id', $user->id)
            ->whereNotNull('response_time_ms')
            ->avg('response_time_ms');

        return view('statistics', compact(
            'verificationsByDay',
            'verificationsByType',
            'avgResponseTime'
        ));
    }
}