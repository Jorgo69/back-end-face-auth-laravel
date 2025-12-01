<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompareImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Accessible à tous
    }

    public function rules(): array
    {
        return [
            'image1' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp',
                'max:10240',
            ],
            'image2' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'image1.required' => 'Veuillez fournir la première image.',
            'image1.mimes' => 'Formats acceptés pour l\'image 1 : JPEG, PNG, WebP.',
            'image1.max' => 'L\'image 1 ne doit pas dépasser 10 MB.',
            'image2.required' => 'Veuillez fournir la deuxième image.',
            'image2.mimes' => 'Formats acceptés pour l\'image 2 : JPEG, PNG, WebP.',
            'image2.max' => 'L\'image 2 ne doit pas dépasser 10 MB.',
        ];
    }
}