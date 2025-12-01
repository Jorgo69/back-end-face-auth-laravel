@extends('layouts.app') 

@section('title', 'Comparaison de Deux Visages')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <header class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Comparer Deux Images</h1>
        <p class="text-gray-600">Téléchargez deux images pour vérifier si elles contiennent le même visage (appel à l'API Python via la méthode `compareTwoImages`).</p>
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

    <form method="POST" action="{{ route('compare.submit') }}" enctype="multipart/form-data" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- Image 1 --}}
            <div>
                <h2 class="text-xl font-semibold mb-3 text-indigo-700 border-b pb-2">Image 1 (Référence) *</h2>
                
                <label for="image1" class="block text-sm font-medium text-gray-700">Fichier Image</label>
                <p class="text-xs text-gray-500 mb-2">Première image pour l'extraction du jeton.</p>
                
                <input type="file" name="image1" id="image1" accept="image/jpeg,image/png,image/webp" required
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('image1') border-red-500 @enderror">
                
                @error('image1')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image 2 --}}
            <div>
                <h2 class="text-xl font-semibold mb-3 text-indigo-700 border-b pb-2">Image 2 (Test) *</h2>
                
                <label for="image2" class="block text-sm font-medium text-gray-700">Fichier Image</label>
                <p class="text-xs text-gray-500 mb-2">Deuxième image pour la vérification de la correspondance.</p>
                
                <input type="file" name="image2" id="image2" accept="image/jpeg,image/png,image/webp" required
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 @error('image2') border-red-500 @enderror">
                
                @error('image2')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Bouton de soumission --}}
        <div class="pt-6 border-t border-gray-200 text-center">
            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-lg font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Lancer la Comparaison
            </button>
        </div>
    </form>
</div>
@endsection