<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Services\FaceAuthService;
use App\Services\ImageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonController extends Controller
{
    use ApiResponse;

    public function __construct(
        private FaceAuthService $faceAuthService,
        private ImageService $imageService
    ) {
        $this->middleware('auth:sanctum');
    }

    /**
     * Liste des personnes
     */
    public function index(Request $request): JsonResponse
    {
        $query = Person::where('user_id', Auth::id());

        // Recherche
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filtre actif/inactif
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Tri
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $persons = $query->paginate($request->input('per_page', 20));

        return $this->paginated(
            PersonResource::collection($persons),
            'Liste des personnes récupérée'
        );
    }

    /**
     * Créer une personne
     */
    public function store(StorePersonRequest $request): JsonResponse
    {
        // Valider l'image
        $validation = $this->imageService->validate($request->file('image'));
        
        if (!$validation['valid']) {
            return $this->validationError(['image' => $validation['errors']]);
        }

        // Sauvegarder l'image
        $imageResult = $this->imageService->store($request->file('image'), 'persons');
        
        if (!$imageResult['success']) {
            return $this->error('Échec de la sauvegarde de l\'image', 500);
        }

        // Générer le token
        $tokenResult = $this->faceAuthService->tokenize($request->file('image'));

        if (!$tokenResult['success']) {
            $this->imageService->delete($imageResult['path']);
            return $this->error($tokenResult['error'], 400);
        }

        // Créer la personne
        $person = Person::create([
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'notes' => $request->notes,
            'face_token' => $tokenResult['token'],
            'face_age' => $tokenResult['age'],
            'face_gender' => $tokenResult['gender'],
            'image_path' => $imageResult['path'],
            'image_original_name' => $imageResult['original_name'],
            'registered_at' => now(),
        ]);

        return $this->created(
            new PersonResource($person),
            'Personne créée avec succès'
        );
    }

    /**
     * Afficher une personne
     */
    public function show(Person $person): JsonResponse
    {
        $this->authorize('view', $person);

        return $this->success(
            new PersonResource($person->load('user')),
            'Détails de la personne'
        );
    }

    /**
     * Mettre à jour une personne
     */
    public function update(Request $request, Person $person): JsonResponse
    {
        $this->authorize('update', $person);

        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $person->update($validated);

        return $this->updated(
            new PersonResource($person),
            'Personne mise à jour'
        );
    }

    /**
     * Supprimer une personne
     */
    public function destroy(Person $person): JsonResponse
    {
        $this->authorize('delete', $person);

        $person->delete();

        return $this->deleted('Personne supprimée');
    }

    /**
     * Vérifier une personne avec une nouvelle image
     */
    public function verify(Request $request, Person $person): JsonResponse
    {
        $this->authorize('view', $person);

        $request->validate([
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        $result = $this->faceAuthService->verify(
            $request->file('image'),
            $person->face_token,
            [
                'type' => \App\Models\FaceVerification::TYPE_PERSON_MATCH,
                'user_id' => Auth::id(),
                'person_id' => $person->id,
            ]
        );

        if (!$result['success']) {
            return $this->error($result['error'], 400);
        }

        return $this->success([
            'match_found' => $result['match_found'],
            'detail' => $result['detail'],
            'person' => new PersonResource($person),
            'response_time_ms' => $result['response_time_ms'],
        ], $result['match_found'] ? 'Correspondance trouvée' : 'Aucune correspondance');
    }
}