@extends('layouts.app') 

@section('title', 'Connexion Faciale')

@section('content')
<div class="max-w-md mx-auto p-8 mt-10 bg-white rounded-lg shadow-xl border border-indigo-100">
    
    <header class="text-center mb-6">
        <h1 class="text-3xl font-bold text-indigo-700">🔒 Connexion par Visage</h1>
        <p class="text-gray-600 mt-2">Entrez votre email et téléchargez votre photo pour vous connecter.</p>
    </header>

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <p class="font-bold">Erreur(s) de validation :</p>
            <ul class="mt-1 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('face-auth.login.submit') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Champ Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
            <input type="email" name="email" id="email" required 
                   value="{{ old('email') }}"
                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Image File Input --}}
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Image à Vérifier *</label>
            
            <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp" required
                   class="mt-1 block w-full text-sm text-gray-500 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 @error('image') border-red-500 @enderror">
            
            @error('image')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <button type="submit" class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Me Connecter
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-indigo-600">
            Se connecter avec mot de passe standard
        </a>
    </div>
</div>
@endsection