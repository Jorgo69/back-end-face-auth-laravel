@extends('layouts.app') 
{{-- Assurez-vous d'avoir un layout de base --}}

@section('title', 'Enregistrer une nouvelle Personne')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Nouvel Enregistrement (Personne)</h1>
        <p class="text-gray-600">Ajoutez les informations de la personne et son image de référence pour la reconnaissance faciale.</p>
    </header>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Oups!</strong>
            <span class="block sm:inline"> Veuillez corriger les erreurs suivantes.</span>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- L'attribut 'enctype="multipart/form-data"' est crucial pour l'envoi de fichiers --}}
    <form method="POST" action="{{ route('persons.store') }}" enctype="multipart/form-data" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Section 1: Informations Personnelles --}}
            <div>
                <h2 class="text-xl font-semibold mb-3 text-gray-800 border-b pb-2">Détails Personnels</h2>
                
                {{-- Prénom --}}
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom *</label>
                    <input type="text" name="first_name" id="first_name" required 
                           value="{{ old('first_name') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nom --}}
                <div class="mt-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                    <input type="text" name="last_name" id="last_name" required 
                           value="{{ old('last_name') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email (Optionnel)</label>
                    <input type="email" name="email" id="email" 
                           value="{{ old('email') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Téléphone --}}
                <div class="mt-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone (Optionnel)</label>
                    <input type="text" name="phone" id="phone" 
                           value="{{ old('phone') }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Section 2: Image et Notes --}}
            <div>
                <h2 class="text-xl font-semibold mb-3 text-gray-800 border-b pb-2">Image de Référence Faciale</h2>
                
                {{-- Image File Input --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Image du Visage *</label>
                    <p class="text-xs text-gray-500 mb-2">JPEG, PNG ou WebP. Min 200x200, Max 10MB.</p>
                    
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" required
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('image') border-red-500 @enderror">
                    
                    @error('image')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optionnel)</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Statut Actif (Par défaut à 1, mais peut être géré par le contrôleur) --}}
                <input type="hidden" name="is_active" value="1">
                
            </div>
        </div>

        {{-- Bouton de soumission --}}
        <div class="pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Enregistrer la Personne et Tokeniser le Visage
            </button>
        </div>
    </form>
</div>
@endsection