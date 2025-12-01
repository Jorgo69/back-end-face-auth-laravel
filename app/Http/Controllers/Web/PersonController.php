<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonRequest;
use App\Models\Person;
use App\Services\FaceAuthService;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function __construct(
        private FaceAuthService $faceAuthService,
        private ImageService $imageService
    ) {
        $this->middleware('auth');
    }

    /**
     * Liste des personnes
     */
    public function index(Request $request): View
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

        $persons = $query->latest()->paginate(20);

        return view('persons.index', compact('persons'));
    }

    /**
     * Formulaire d'ajout
     */
    public function create(): View
    {
        return view('persons.create');
    }

    /**
     * Enregistrer une nouvelle personne
     */
    public function store(StorePersonRequest $request): RedirectResponse
    {
        // Valider l'image
        $validation = $this->imageService->validate($request->file('image'));
        
        if (!$validation['valid']) {
            return back()
                ->withErrors(['image' => implode(' ', $validation['errors'])])
                ->withInput();
        }

        // Sauvegarder l'image
        $imageResult = $this->imageService->store($request->file('image'), 'persons');
        
        if (!$imageResult['success']) {
            return back()
                ->withErrors(['image' => 'Échec de la sauvegarde de l\'image'])
                ->withInput();
        }

        // Générer le token via l'API Python
        $tokenResult = $this->faceAuthService->tokenize($request->file('image'));

        if (!$tokenResult['success']) {
            // Supprimer l'image si le token échoue
            $this->imageService->delete($imageResult['path']);
            
            return back()
                ->withErrors(['image' => $tokenResult['error']])
                ->withInput();
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

        return redirect()
            ->route('persons.show', $person)
            ->with('success', '✅ Personne ajoutée avec succès !');
    }

    /**
     * Afficher une personne
     */
    public function show(Person $person): View
    {
        $this->authorize('view', $person);

        // Historique des vérifications
        $verifications = $person->verifications()
            ->latest()
            ->paginate(10);

        return view('persons.show', compact('person', 'verifications'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Person $person): View
    {
        $this->authorize('update', $person);

        return view('persons.edit', compact('person'));
    }

    /**
     * Mettre à jour une personne
     */
    public function update(Request $request, Person $person): RedirectResponse
    {
        $this->authorize('update', $person);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['boolean'],
        ]);

        $person->update($validated);

        return redirect()
            ->route('persons.show', $person)
            ->with('success', '✅ Personne mise à jour avec succès !');
    }

    /**
     * Supprimer une personne
     */
    public function destroy(Person $person): RedirectResponse
    {
        $this->authorize('delete', $person);

        $person->delete();

        return redirect()
            ->route('persons.index')
            ->with('success', '✅ Personne supprimée avec succès.');
    }

    /**
     * Vérifier une personne avec une nouvelle image
     */
    public function verify(Request $request, Person $person): RedirectResponse
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
            return back()->with('error', '❌ ' . $result['error']);
        }

        if ($result['match_found']) {
            return back()->with('success', '✅ Correspondance trouvée ! Il s\'agit bien de ' . $person->full_name);
        } else {
            return back()->with('warning', '⚠️ Aucune correspondance. Ce n\'est pas ' . $person->full_name);
        }
    }
}