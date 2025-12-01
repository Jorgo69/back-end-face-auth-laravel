<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFaceRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'image' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp',
                'max:10240', // 10MB
            ],
        ];
    }

    /**
     * Messages personnalisés
     */
    public function messages(): array
    {
        return [
            'image.required' => 'Veuillez fournir une image de votre visage.',
            'image.file' => 'Le fichier doit être une image valide.',
            'image.mimes' => 'Formats acceptés : JPEG, PNG, WebP.',
            'image.max' => 'L\'image ne doit pas dépasser 10 MB.',
        ];
    }
}