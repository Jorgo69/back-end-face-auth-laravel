@extends('layouts.app')

@section('title', 'Détails de ' . $person->full_name)

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

    {{-- Messages de Session (Succès, Erreur, Avertissement) --}}
    @if (session('success') || session('error') || session('warning'))
        <div class="mb-6 @if(session('success')) bg-green-100 border-green-400 text-green-700 @elseif(session('warning')) bg-yellow-100 border-yellow-400 text-yellow-700 @else bg-red-100 border-red-400 text-red-700 @endif px-4 py-3 rounded relative border">
            <p class="font-bold">Résultat de la Vérification :</p>
            <p>{{ session('success') ?? session('warning') ?? session('error') }}</p>
        </div>
    @endif

    <header class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">
            Détails de la Personne : **{{ $person->full_name }}**
        </h1>
        <div class="flex space-x-3">
            <a href="{{ route('persons.edit', $person) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                Éditer
            </a>
            
            {{-- Formulaire de Suppression (avec confirmation simple) --}}
            <form action="{{ route('persons.destroy', $person) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette personne ? Cette action est irréversible et supprimera l\'image associée.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Supprimer
                </button>
            </form>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- COLONNE 1 & 2: Infos et Vérification --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 1. Informations de Base --}}
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Informations de Référence</h2>

                <div class="flex space-x-6">
                    {{-- Image de Référence --}}
                    <div>
                        <img src="{{ $person->image_url ?? asset('images/default-face.png') }}" 
                             alt="Image de référence" 
                             class="h-40 w-40 object-cover rounded-md border-2 border-indigo-500 shadow-lg">
                        <p class="mt-2 text-xs text-gray-500 text-center">Enregistré le {{ $person->registered_at->format('d/m/Y') }}</p>
                    </div>
                    
                    {{-- Détails --}}
                    <dl class="space-y-3 flex-grow">
                        <div class="flex items-center">
                            <dt class="text-sm font-medium text-gray-500 w-32">Statut:</dt>
                            <dd class="text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $person->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $person->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Contact:</dt>
                            <dd class="text-sm text-gray-900">{{ $person->email ?? 'N/A' }} / {{ $person->phone ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Âge/Genre (API):</dt>
                            <dd class="text-sm text-gray-900">
                                {{ $person->face_age ?? 'N/A' }} ans / {{ $person->face_gender === 'M' ? 'Homme' : ($person->face_gender === 'F' ? 'Femme' : 'N/A') }}
                            </dd>
                        </div>
                        <div class="flex">
                            <dt class="text-sm font-medium text-gray-500 w-32">Notes:</dt>
                            <dd class="text-sm text-gray-900 break-words max-w-sm">{{ $person->notes ?? 'Aucune' }}</dd>
                        </div>
                        <div class="pt-2 border-t mt-4">
                             <dt class="text-xs font-mono text-gray-400 truncate">Token Référence (API) :</dt>
                             <dd class="text-xs font-mono text-gray-700 truncate cursor-pointer hover:text-indigo-600" title="{{ $person->face_token ?? 'Token non généré' }}">
                                {{ substr($person->face_token, 0, 40) . '...' ?? 'N/A' }}
                             </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- 2. Formulaire de Vérification (Match) --}}
            <div class="bg-indigo-50 p-6 rounded-lg shadow-md border border-indigo-200">
                <h2 class="text-xl font-semibold mb-4 text-indigo-800 border-b border-indigo-200 pb-2">Tester la Reconnaissance Faciale</h2>
                <p class="text-sm text-indigo-700 mb-4">Téléchargez une nouvelle image pour vérifier si elle correspond au visage de **{{ $person->first_name }}** (appel à `/face/match`).</p>

                <form action="{{ route('persons.verify', $person) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="image_verify" class="block text-sm font-medium text-indigo-700">Image à Vérifier *</label>
                        <input type="file" name="image" id="image_verify" accept="image/jpeg,image/png,image/webp" required
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 @error('image') border-red-500 @enderror">
                        
                        @error('image')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Vérifier la Correspondance
                    </button>
                </form>
            </div>
            
        </div>
        
        {{-- COLONNE 3: Historique des Vérifications --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">Historique des Vérifications</h2>

                @forelse ($verifications as $verification)
                <div class="flex items-center space-x-3 py-3 border-b border-gray-100 last:border-b-0">
                    
                    {{-- Icône de Statut --}}
                    <div class="flex-shrink-0">
                        @if ($verification->isSuccessful())
                            <span class="text-green-500 text-xl" title="Succès">✅</span>
                        @elseif ($verification->status === \App\Models\FaceVerification::STATUS_FAILED)
                            <span class="text-yellow-500 text-xl" title="Échec">⚠️</span>
                        @else
                            <span class="text-red-500 text-xl" title="Erreur">❌</span>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $verification->type_label ?? $verification->type }}
                        </p>
                        <p class="text-xs text-gray-500 truncate" title="{{ $verification->detail }}">
                            {{ Str::limit($verification->detail ?? 'Aucun détail', 40) }}
                        </p>
                    </div>

                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ $verification->created_at->diffForHumans() }}</p>
                        <p class="text-xs text-gray-400">{{ round($verification->response_time_ms / 1000, 2) }}s</p>
                    </div>

                </div>
                @empty
                    <p class="text-gray-500 text-sm">Aucune vérification enregistrée pour cette personne.</p>
                @endforelse

                {{-- Pagination de l'historique --}}
                <div class="mt-4">
                    {{ $verifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection