@extends('layouts.app')

@section('title', 'Réenregistrement Facial')

@section('content')
<div class="max-w-md mx-auto p-8 mt-10 bg-white rounded-lg shadow-xl border border-red-100">
    
    <header class="text-center mb-6">
        <h1 class="text-3xl font-bold text-red-700">🔄 Mettre à Jour Mon Visage</h1>
        <p class="text-gray-600 mt-2">Ceci remplacera votre ancienne image de référence pour la connexion faciale.</p>
    </header>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <p class="font-bold">Erreur(s) :</p>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('face-auth.reregister.submit') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Avertissement --}}
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-md">
            <p class="text-sm text-yellow-700 font-medium">Attention : Un nouveau jeton sera généré, remplaçant l'ancien. Votre visage doit être bien visible.</p>
        </div>

        {{-- Image File Input --}}
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Nouvelle Image du Visage *</label>
            
            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" required
                   class="mt-1 block w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-red-100 file:text-red-700 hover:file:bg-red-200 @error('image') border-red-500 @enderror">
            
            @error('image')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <button type="submit" class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            Mettre à Jour Mon Visage
        </button>
    </form>
</div>
@endsection