@extends('layouts.app')

@section('title', 'Enregistrer mon visage')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- En-tête -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Enregistrer mon visage</h1>
        <p class="mt-2 text-sm text-gray-600">
            Prenez une photo de votre visage pour activer la connexion biométrique.
        </p>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-8 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Conseils pour une bonne capture :</h3>
                <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                    <li>Assurez-vous d'être dans un endroit bien éclairé</li>
                    <li>Regardez directement la caméra</li>
                    <li>Évitez les lunettes de soleil ou masques</li>
                    <li>Restez immobile pendant la capture</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Formulaire avec webcam -->
    <form action="{{ route('face-auth.register.submit') }}" method="POST" enctype="multipart/form-data" id="faceRegisterForm" class="bg-white shadow-sm rounded-lg overflow-hidden">
        @csrf
        
        <div class="p-6">
            <!-- Choix du mode : Webcam ou Upload -->
            <div class="mb-6">
                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <button type="button" onclick="switchMode('webcam')" id="webcamModeBtn" class="flex-1 py-3 px-4 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Utiliser la webcam
                    </button>
                    <button type="button" onclick="switchMode('upload')" id="uploadModeBtn" class="flex-1 py-3 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Uploader une photo
                    </button>
                </div>
            </div>

            <!-- Mode Webcam -->
            <div id="webcamMode" class="space-y-6">
                <!-- Vidéo webcam -->
                <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                    <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
                    
                    <!-- Overlay avec guide -->
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="w-64 h-80 border-4 border-white border-dashed rounded-full opacity-50"></div>
                        </div>
                    </div>

                    <!-- Canvas caché pour la capture -->
                    <canvas id="canvas" class="hidden"></canvas>
                </div>

                <!-- Aperçu de la photo capturée -->
                <div id="preview" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Photo capturée :</label>
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden" style="aspect-ratio: 4/3;">
                        <img id="capturedImage" src="" alt="Photo capturée" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Boutons d'action webcam -->
                <div class="flex items-center space-x-4">
                    <button type="button" onclick="startWebcam()" id="startBtn" class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Démarrer la caméra
                    </button>
                    <button type="button" onclick="capturePhoto()" id="captureBtn" class="hidden flex-1 bg-indigo-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Prendre la photo
                    </button>
                    <button type="button" onclick="retakePhoto()" id="retakeBtn" class="hidden flex-1 bg-yellow-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-yellow-700 transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reprendre
                    </button>
                </div>

                <!-- Input caché pour stocker l'image -->
                <input type="hidden" name="image_data" id="imageData">
            </div>

            <!-- Mode Upload -->
            <div id="uploadMode" class="hidden">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    Sélectionnez une photo de votre visage
                </label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                <span>Cliquez pour sélectionner</span>
                                <input id="image" name="image" type="file" accept="image/jpeg,image/jpg,image/png,image/webp" class="sr-only" onchange="previewUpload(this)">
                            </label>
                            <p class="pl-1">ou glissez-déposez</p>
                        </div>
                        <p class="text-xs text-gray-500">JPEG, PNG ou WebP jusqu'à 10MB</p>
                    </div>
                </div>

                <!-- Prévisualisation upload -->
                <div id="uploadPreview" class="hidden mt-4">
                    <img id="uploadedImage" src="" alt="Aperçu" class="max-h-96 mx-auto rounded-lg shadow-lg">
                </div>
            </div>

            <!-- Erreurs -->
            @if ($errors->any())
                <div class="mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erreur(s) de validation :</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer avec bouton de soumission -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                ← Retour au dashboard
            </a>
            <button type="submit" id="submitBtn" class="bg-indigo-600 text-white py-3 px-8 rounded-lg font-medium hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer mon visage
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let stream = null;
let currentMode = 'webcam';

// Basculer entre webcam et upload
function switchMode(mode) {
    currentMode = mode;
    
    if (mode === 'webcam') {
        document.getElementById('webcamMode').classList.remove('hidden');
        document.getElementById('uploadMode').classList.add('hidden');
        document.getElementById('webcamModeBtn').classList.remove('bg-gray-200', 'text-gray-700');
        document.getElementById('webcamModeBtn').classList.add('bg-indigo-600', 'text-white');
        document.getElementById('uploadModeBtn').classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('uploadModeBtn').classList.add('bg-gray-200', 'text-gray-700');
        
        // Désactiver l'input file
        document.getElementById('image').disabled = true;
    } else {
        document.getElementById('webcamMode').classList.add('hidden');
        document.getElementById('uploadMode').classList.remove('hidden');
        document.getElementById('uploadModeBtn').classList.remove('bg-gray-200', 'text-gray-700');
        document.getElementById('uploadModeBtn').classList.add('bg-indigo-600', 'text-white');
        document.getElementById('webcamModeBtn').classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('webcamModeBtn').classList.add('bg-gray-200', 'text-gray-700');
        
        // Activer l'input file
        document.getElementById('image').disabled = false;
        
        // Arrêter la webcam si active
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }
}

// Démarrer la webcam
async function startWebcam() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 1280 },
                height: { ideal: 960 },
                facingMode: 'user'
            } 
        });
        
        const video = document.getElementById('webcam');
        video.srcObject = stream;
        
        // Mettre à jour les boutons
        document.getElementById('startBtn').classList.add('hidden');
        document.getElementById('captureBtn').classList.remove('hidden');
        
    } catch (err) {
        console.error('Erreur webcam:', err);
        alert('Impossible d\'accéder à la caméra. Veuillez vérifier les permissions ou utiliser le mode Upload.');
    }
}

// Capturer la photo
function capturePhoto() {
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    
    // Définir les dimensions du canvas
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Dessiner l'image
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convertir en base64
    const imageData = canvas.toDataURL('image/jpeg', 0.95);
    document.getElementById('imageData').value = imageData;
    
    // Afficher l'aperçu
    document.getElementById('capturedImage').src = imageData;
    document.getElementById('preview').classList.remove('hidden');
    
    // Cacher la vidéo et mettre à jour les boutons
    video.classList.add('hidden');
    document.getElementById('captureBtn').classList.add('hidden');
    document.getElementById('retakeBtn').classList.remove('hidden');
    
    // Arrêter le flux vidéo
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
}

// Reprendre une photo
function retakePhoto() {
    const video = document.getElementById('webcam');
    
    video.classList.remove('hidden');
    document.getElementById('preview').classList.add('hidden');
    document.getElementById('retakeBtn').classList.add('hidden');
    document.getElementById('imageData').value = '';
    
    // Redémarrer la webcam
    startWebcam();
}

// Prévisualiser l'upload
function previewUpload(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('uploadedImage').src = e.target.result;
            document.getElementById('uploadPreview').classList.remove('hidden');
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Validation avant soumission
document.getElementById('faceRegisterForm').addEventListener('submit', function(e) {
    if (currentMode === 'webcam') {
        const imageData = document.getElementById('imageData').value;
        
        if (!imageData) {
            e.preventDefault();
            alert('Veuillez prendre une photo avant de soumettre.');
            return false;
        }
        
        // Convertir base64 en File pour l'upload
        fetch(imageData)
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], 'webcam-capture.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('image').files = dataTransfer.files;
            });
    } else {
        const fileInput = document.getElementById('image');
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner une image.');
            return false;
        }
    }
    
    // Désactiver le bouton pour éviter les doubles soumissions
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<svg class="animate-spin h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Traitement en cours...';
});

// Nettoyer au déchargement de la page
window.addEventListener('beforeunload', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
});
</script>
@endpush
@endsection