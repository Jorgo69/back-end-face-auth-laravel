@extends('layouts.app') 

@section('title', 'Éditer ' . $person->full_name)

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <header class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Éditer : **{{ $person->full_name }}**</h1>
        <a href="{{ route('persons.show', $person) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
            Retour aux Détails
        </a>
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

    <form method="POST" action="{{ route('persons.update', $person) }}" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT') {{-- Utiliser la méthode PUT pour la mise à jour --}}

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Section 1: Informations Personnelles --}}
            <div>
                <h2 class="text-xl font-semibold mb-3 text-gray-800 border-b pb-2">Détails Personnels</h2>
                
                {{-- Prénom --}}
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom *</label>
                    <input type="text" name="first_name" id="first_name" required 
                           value="{{ old('first_name', $person->first_name) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nom --}}
                <div class="mt-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                    <input type="text" name="last_name" id="last_name" required 
                           value="{{ old('last_name', $person->last_name) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email (Optionnel)</label>
                    <input type="email" name="email" id="email" 
                           value="{{ old('email', $person->email) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Téléphone --}}
                <div class="mt-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone (Optionnel)</label>
                    <input type="text" name="phone" id="phone" 
                           value="{{ old('phone', $person->phone) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Section 2: Image et Contrôles --}}
            <div>
                <h2 class="text-xl font-semibold mb-3 text-gray-800 border-b pb-2">Image et Contrôles</h2>
                
                {{-- Image de Référence Actuelle --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Image Actuelle</label>
                    <img src="{{ $person->image_url ?? asset('images/default-face.png') }}" 
                         alt="Image de référence actuelle" 
                         class="h-24 w-24 object-cover rounded-md border border-gray-300">
                    <p class="text-xs text-gray-500 mt-1">L'image faciale ne peut pas être changée ici. Vous devez recréer la personne pour mettre à jour le jeton facial.</p>
                </div>
                
                {{-- Notes --}}
                <div class="mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optionnel)</label>
                    <textarea name="notes" id="notes" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-500 @enderror">{{ old('notes', $person->notes) }}</textarea>
                    @error('notes')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Statut Actif --}}
                <div class="mt-6 flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                           {{ old('is_active', $person->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">
                        Actif (Peut être vérifié)
                    </label>
                </div>
                
            </div>
        </div>

        {{-- Bouton de soumission --}}
        <div class="pt-6 border-t border-gray-200">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Sauvegarder les Modifications
            </button>
        </div>
    </form>
</div>
@endsection