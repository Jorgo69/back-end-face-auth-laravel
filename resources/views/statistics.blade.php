@extends('layouts.app')

@section('title', 'Statistiques Détaillées')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

    <header class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Statistiques Détaillées</h1>
        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Retour au Dashboard
        </a>
    </header>

    {{-- Indicateurs Clés --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        {{-- Temps de Réponse Moyen --}}
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-yellow-500">
            <p class="text-sm font-medium text-gray-500">Temps de Réponse Moyen API</p>
            <div class="mt-1 text-3xl font-semibold text-gray-900">
                {{ round($avgResponseTime ?? 0, 2) }} ms
            </div>
            <p class="text-xs text-gray-500 mt-1">Moyenne calculée sur toutes les vérifications.</p>
        </div>

        {{-- Total Succès --}}
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-500">
            <p class="text-sm font-medium text-gray-500">Taux de Succès Total</p>
            @php
                $totalCount = $verificationsByType->sum('count');
                $successCount = $verificationsByType->where('type', \App\Models\FaceVerification::TYPE_PERSON_MATCH)->firstWhere('count') ?? 0;
                $successRate = $totalCount > 0 ? round(($successCount / $totalCount) * 100, 1) : 0;
            @endphp
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $successRate }} %</div>
            <p class="text-xs text-gray-500 mt-1">Basé sur les vérifications de correspondance (Match).</p>
        </div>
        
        {{-- Total Vérifications --}}
        <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-gray-500">
            <p class="text-sm font-medium text-gray-500">Total des Opérations Faciales</p>
            <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalCount ?? 0 }}</div>
            <p class="text-xs text-gray-500 mt-1">Inclus connexion, vérification, et comparaison.</p>
        </div>
    </div>
    
    ---

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- 1. Vérifications par Type (Graphe ou Liste) --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Vérifications par Type</h2>
            <dl class="space-y-3">
                @foreach ($verificationsByType as $typeStat)
                <div class="flex justify-between items-center py-1">
                    <dt class="text-sm font-medium text-gray-600">{{ $typeStat->type_label ?? $typeStat->type }}</dt>
                    <dd class="text-lg font-bold text-indigo-600">{{ $typeStat->count }}</dd>
                </div>
                @endforeach
            </dl>
        </div>

        {{-- 2. Vérifications par Jour (Graphe) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Activité sur les 7 Derniers Jours</h2>
            
            {{-- Canvas Chart.js --}}
            <div class="h-64">
                <canvas id="verificationsChart"></canvas>
            </div>
        </div>

    </div>

</div>

@push('scripts')
{{-- IMPORTANT: Ceci nécessite l'inclusion de Chart.js dans votre layout/application --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script>
    const ctx = document.getElementById('verificationsChart').getContext('2d');
    
    // Préparation des données pour Chart.js
    const data = @json($verificationsByDay);
    const labels = data.map(item => item.date);
    const totalData = data.map(item => item.count);
    const successData = data.map(item => item.success);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Total Vérifications',
                    data: totalData,
                    backgroundColor: 'rgba(99, 102, 241, 0.7)', // Indigo
                    borderColor: 'rgb(99, 102, 241)',
                    borderWidth: 1
                },
                {
                    label: 'Succès (Match Found)',
                    data: successData,
                    backgroundColor: 'rgba(16, 185, 129, 0.7)', // Green
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection