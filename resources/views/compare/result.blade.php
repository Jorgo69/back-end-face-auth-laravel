@extends('layouts.app') 

@section('title', 'Résultat de la Comparaison')

@section('content')
<div class="max-w-4xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <header class="mb-8 text-center">
        @if ($comparisonResult['match_found'])
            <h1 class="text-4xl font-extrabold text-green-600">✅ Correspondance Trouvée !</h1>
            <p class="mt-2 text-xl text-green-700">Les deux visages sont considérés comme la même personne.</p>
        @else
            <h1 class="text-4xl font-extrabold text-red-600">❌ Aucune Correspondance</h1>
            <p class="mt-2 text-xl text-red-700">Les deux visages ne correspondent pas.</p>
        @endif
    </header>

    <div class="bg-white p-8 rounded-lg shadow-xl border border-gray-100 space-y-6">
        
        <h2 class="text-2xl font-semibold border-b pb-2 text-gray-800">Détails de la Vérification</h2>

        <dl class="space-y-4">
            <div class="flex items-center justify-between border-b pb-2">
                <dt class="text-lg font-medium text-gray-500">Statut de la Correspondance :</dt>
                <dd class="text-xl font-bold @if ($comparisonResult['match_found']) text-green-600 @else text-red-600 @endif">
                    {{ $comparisonResult['match_found'] ? 'OUI' : 'NON' }}
                </dd>
            </div>
            
            <div class="flex items-center justify-between border-b pb-2">
                <dt class="text-lg font-medium text-gray-500">Message de l'API :</dt>
                <dd class="text-lg text-gray-900 text-right max-w-sm">{{ $comparisonResult['detail'] ?? 'N/A' }}</dd>
            </div>
            
            <div class="flex items-center justify-between pt-2">
                <dt class="text-lg font-medium text-gray-500">Temps de Réponse API :</dt>
                <dd class="text-lg font-mono text-gray-900">{{ round($comparisonResult['response_time_ms'] / 1000, 3) }} s</dd>
            </div>
        </dl>

        <div class="pt-6 border-t border-gray-200 text-center">
            <a href="{{ route('compare.index') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                ⬅️ Effectuer une Nouvelle Comparaison
            </a>
        </div>
    </div>
</div>
@endsection