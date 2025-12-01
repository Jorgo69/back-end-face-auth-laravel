@extends('layouts.app')

@section('title', 'Visage Déjà Enregistré')

@section('content')
<div class="max-w-md mx-auto p-8 mt-10 text-center bg-yellow-50 rounded-lg shadow-xl border border-yellow-200">
    
    <h1 class="text-3xl font-bold text-yellow-800 mb-4">⚠️ Visage Déjà Enregistré</h1>
    
    <p class="text-gray-700 mb-6">
        Votre visage a déjà été enregistré le 
        @if ($user->face_registered_at)
            **{{ $user->face_registered_at->format('d/m/Y à H:i') }}**.
        @else
            **une date inconnue**.
        @endif
    </p>

    <div class="space-y-4">
        <a href="{{ route('dashboard') }}" class="w-full inline-flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
            Retour au Dashboard
        </a>
        
        <p class="text-sm text-gray-600">Si vous souhaitez mettre à jour votre photo :</p>
        <a href="{{ route('face-auth.reregister') }}" class="w-full inline-flex justify-center py-2 px-4 border border-indigo-600 text-sm font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50">
            Réenregistrer Mon Visage
        </a>
    </div>

</div>
@endsection