<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaceVerificationResource;
use App\Models\FaceVerification;
use App\Models\Person;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    use ApiResponse;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Dashboard : Statistiques globales
     */
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            // Compte utilisateur
            'user' => [
                'has_face_registered' => $user->hasFaceRegistered(),
                'face_registered_at' => $user->face_registered_at?->toISOString(),
                'verification_count' => $user->face_verification_count,
                'last_verified_at' => $user->face_last_verified_at?->toISOString(),
            ],
            
            // Personnes
            'persons' => [
                'total' => Person::where('user_id', $user->id)->count(),
                'active' => Person::where('user_id', $user->id)->active()->count(),
            ],
            
            // Vérifications
            'verifications' => [
                'today' => FaceVerification::where('user_id', $user->id)
                    ->whereDate('created_at', today())
                    ->count(),
                'week' => FaceVerification::where('user_id', $user->id)
                    ->whereBetween('created_at', [now()->subWeek(), now()])
                    ->count(),
                'month' => FaceVerification::where('user_id', $user->id)
                    ->whereBetween('created_at', [now()->subMonth(), now()])
                    ->count(),
                'success_rate' => $this->calculateSuccessRate($user->id),
            ],
            
            // Performance
            'performance' => [
                'avg_response_time_ms' => FaceVerification::where('user_id', $user->id)
                    ->whereNotNull('response_time_ms')
                    ->avg('response_time_ms'),
            ],
        ];

        return $this->success($stats, 'Statistiques récupérées');
    }

    /**
     * Historique des vérifications
     */
    public function verifications(Request $request): JsonResponse
    {
        $query = FaceVerification::where('user_id', Auth::id());

        // Filtre par type
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Filtre par statut
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filtre par période
        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', $to);
        }

        $verifications = $query->with(['person'])
            ->latest()
            ->paginate($request->input('per_page', 20));

        return $this->paginated(
            FaceVerificationResource::collection($verifications),
            'Historique récupéré'
        );
    }

    /**
     * Graphique : Vérifications par jour
     */
    public function verificationsByDay(Request $request): JsonResponse
    {
        $days = $request->input('days', 7);
        
        $data = FaceVerification::where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->subDays($days), now()])
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(match_found) as success')
            ->selectRaw('SUM(CASE WHEN match_found = 0 THEN 1 ELSE 0 END) as failed')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $this->success([
            'period' => "{$days} derniers jours",
            'data' => $data,
        ], 'Graphique généré');
    }

    /**
     * Graphique : Vérifications par type
     */
    public function verificationsByType(): JsonResponse
    {
        $data = FaceVerification::where('user_id', Auth::id())
            ->selectRaw('type')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(match_found) as success')
            ->groupBy('type')
            ->get();

        return $this->success($data, 'Répartition par type');
    }

    /**
     * Calculer le taux de succès
     */
    private function calculateSuccessRate(string $userId): float
    {
        $total = FaceVerification::where('user_id', $userId)->count();
        
        if ($total === 0) {
            return 0;
        }

        $success = FaceVerification::where('user_id', $userId)
            ->where('match_found', true)
            ->count();

        return round(($success / $total) * 100, 2);
    }
}