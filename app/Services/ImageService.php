<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * Formats d'images acceptés
     */
    const ALLOWED_MIMES = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    
    /**
     * Taille max (10MB comme l'API Python)
     */
    const MAX_SIZE = 10 * 1024; // KB

    /**
     * Valider une image
     */
    public function validate(UploadedFile $image): array
    {
        $errors = [];

        // Vérifier le type MIME
        if (!in_array($image->getMimeType(), self::ALLOWED_MIMES)) {
            $errors[] = 'Format non supporté. Utilisez JPEG, PNG ou WebP.';
        }

        // Vérifier la taille
        if ($image->getSize() > (self::MAX_SIZE * 1024)) {
            $errors[] = 'Image trop grande. Maximum 10 MB.';
        }

        // Vérifier les dimensions
        try {
            $imageInfo = getimagesize($image->getRealPath());
            if ($imageInfo) {
                [$width, $height] = $imageInfo;
                
                if ($width < 200 || $height < 200) {
                    $errors[] = 'Image trop petite. Minimum 200x200 pixels.';
                }
            }
        } catch (\Exception $e) {
            $errors[] = 'Impossible de lire les dimensions de l\'image.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Sauvegarder une image
     */
    public function store(UploadedFile $image, string $directory = 'faces'): array
    {
        try {
            // Générer un nom unique
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path = $directory . '/' . $filename;

            // Sauvegarder l'image originale
            Storage::disk('public')->put($path, file_get_contents($image->getRealPath()));

            return [
                'success' => true,
                'path' => $path,
                'filename' => $filename,
                'original_name' => $image->getClientOriginalName(),
                'url' => Storage::url($path),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Créer une miniature (optionnel, pour la galerie)
     */
    public function createThumbnail(string $path, int $width = 300, int $height = 300): ?string
    {
        try {
            $thumbnailPath = str_replace('.', '_thumb.', $path);
            
            $image = Image::make(Storage::disk('public')->path($path));
            $image->fit($width, $height);
            
            Storage::disk('public')->put(
                $thumbnailPath, 
                (string) $image->encode()
            );

            return $thumbnailPath;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Supprimer une image
     */
    public function delete(string $path): bool
    {
        try {
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtenir les informations d'une image
     */
    public function getInfo(string $path): ?array
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                return null;
            }

            $fullPath = Storage::disk('public')->path($path);
            $imageInfo = getimagesize($fullPath);

            if (!$imageInfo) {
                return null;
            }

            [$width, $height, $type] = $imageInfo;

            return [
                'path' => $path,
                'url' => Storage::url($path),
                'width' => $width,
                'height' => $height,
                'mime_type' => image_type_to_mime_type($type),
                'size' => Storage::disk('public')->size($path),
            ];

        } catch (\Exception $e) {
            return null;
        }
    }
}