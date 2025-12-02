@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600">
            Bienvenue, {{ auth()->user()->name }} ! Voici un aperçu de votre activité.
        </p>
    </div>

    <!-- Statut API Python -->
    <div class="mb-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">API Python FaceAuth</h3>
                    <p class="text-sm text-gray-500">Moteur de reconnaissance faciale</p>
                </div>
                @if($pythonApiHealth['status'] === 'online')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 mr-2 rounded-full bg-green-500 animate-pulse"></span>
                        En ligne
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                        <span class="w-2 h-2 mr-2 rounded-full bg-red-500"></span>
                        Hors ligne
                    </span>
                @endif
            </div>
            @if($pythonApiHealth['status'] === 'online')
                <div class="mt-4 text-sm text-gray-600">
                    <p>Temps de réponse : <span class="font-medium">{{ number_format($pythonApiHealth['response_time_ms'], 2) }} ms</span></p>
                    <p>Modèle chargé : <span class="font-medium">{{ $pythonApiHealth['data']['model_loaded'] ? 'Oui' : 'Non Modèle en veille ' }}</span></p>

                </div>
            @endif
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Visage enregistré -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-md {{ $stats['has_face_registered'] ? 'bg-green-100' : 'bg-yellow-100' }}">
                    <svg class="h-6 w-6 {{ $stats['has_face_registered'] ? 'text-green-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Mon visage</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">
                            {{ $stats['has_face_registered'] ? 'Enregistré' : 'Non enregistré' }}
                        </dd>
                    </dl>
                </div>
            </div>
            @if(!$stats['has_face_registered'])
                <div class="mt-4">
                    <a href="{{ route('face-auth.register') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        Enregistrer mon visage →
                    </a>
                </div>
            @endif
        </div>

        <!-- Vérifications -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-md bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Vérifications</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">{{ $stats['verification_count'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Personnes -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-md bg-purple-100">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Personnes</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">{{ $stats['persons_count'] }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('persons.create') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Ajouter une personne →
                </a>
            </div>
        </div>

        <!-- Vérifications aujourd'hui -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 p-3 rounded-md bg-orange-100">
                    <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Aujourd'hui</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">{{ $stats['verifications_today'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ route('persons.create') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition border-2 border-transparent hover:border-indigo-500">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Ajouter une personne</h3>
                    <p class="text-sm text-gray-500">Enregistrer un nouveau visage</p>
                </div>
            </div>
        </a>

        <a href="{{ route('compare.index') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition border-2 border-transparent hover:border-indigo-500">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Comparer 2 images</h3>
                    <p class="text-sm text-gray-500">Vérifier une correspondance</p>
                </div>
            </div>
        </a>

        <a href="{{ route('statistics') }}" class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition border-2 border-transparent hover:border-indigo-500">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Voir les statistiques</h3>
                    <p class="text-sm text-gray-500">Analytics détaillées</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Dernières vérifications -->
    @if($recentVerifications->count() > 0)
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Dernières vérifications</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($recentVerifications as $verification)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                @if($verification->match_found)
                                    <span class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @else
                                    <span class="flex-shrink-0 h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $verification->type)) }}
                                        @if($verification->person)
                                            - {{ $verification->person->full_name }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $verification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-gray-500">{{ $verification->response_time_ms }} ms</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection