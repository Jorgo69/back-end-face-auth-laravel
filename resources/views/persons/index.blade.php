@extends('layouts.app') 

@section('title', 'Gestion des Personnes')

@section('content')
<div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
    
    <header class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            👥 Personnes Enregistrées 
            <span class="ml-3 text-lg font-normal text-gray-500">({{ $persons->total() }})</span>
        </h1>
        
        <a href="{{ route('persons.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM12 15h2c-2.28 0-4.43 0-6.72 0"></path></svg>
            Ajouter une Personne
        </a>
    </header>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Formulaire de Recherche et Filtre --}}
    <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-gray-100">
        <form method="GET" action="{{ route('persons.index') }}" class="flex space-x-4 items-end">
            
            {{-- Champ de Recherche --}}
            <div class="flex-grow">
                <label for="search" class="sr-only">Rechercher</label>
                <input type="text" name="search" id="search" placeholder="Rechercher par nom ou email..."
                       value="{{ request('search') }}"
                       class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            {{-- Filtre Actif/Inactif --}}
            <div>
                <label for="active" class="block text-sm font-medium text-gray-700">Statut</label>
                <select name="active" id="active" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous</option>
                    <option value="1" @selected(request('active') === '1')>Actif</option>
                    <option value="0" @selected(request('active') === '0')>Inactif</option>
                </select>
            </div>
            
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 h-10">
                Filtrer
            </button>
            @if(request()->has('search') || request()->has('active'))
                <a href="{{ route('persons.index') }}" class="text-sm text-gray-500 hover:text-gray-700 h-10 flex items-center">Réinitialiser</a>
            @endif
        </form>
    </div>

    {{-- Tableau des Personnes --}}
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom Complet</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Âge / Genre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enregistré le</th>
                    <th scope="col" class="relative px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($persons as $person)
                <tr>
                    {{-- Image --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <img src="{{ $person->image_url ?? asset('images/default-face.png') }}" 
                             alt="Visage de {{ $person->full_name }}" 
                             class="h-10 w-10 rounded-full object-cover border border-gray-200">
                    </td>
                    
                    {{-- Nom Complet --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        <a href="{{ route('persons.show', $person) }}" class="text-indigo-600 hover:text-indigo-900">
                            {{ $person->full_name }}
                        </a>
                    </td>
                    
                    {{-- Âge / Genre --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($person->face_age)
                            {{ $person->face_age }} ans / {{ $person->face_gender === 'M' ? 'Homme' : ($person->face_gender === 'F' ? 'Femme' : 'N/A') }}
                        @else
                            N/A
                        @endif
                    </td>
                    
                    {{-- Statut --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $person->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $person->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    
                    {{-- Date d'enregistrement --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $person->registered_at->format('d/m/Y') }}
                    </td>
                    
                    {{-- Actions --}}
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('persons.edit', $person) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Éditer</a>
                        <a href="{{ route('persons.show', $person) }}" class="text-gray-600 hover:text-gray-900">Détails</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Aucune personne enregistrée pour le moment. <a href="{{ route('persons.create') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Ajouter la première personne ici.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $persons->links() }}
    </div>

</div>
@endsection