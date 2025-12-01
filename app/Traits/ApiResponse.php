<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Réponse de succès
     */
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Réponse d'erreur
     */
    protected function error(
        string $message = 'Error',
        int $code = 400,
        mixed $errors = null
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Réponse avec pagination
     */
    protected function paginated($resource, string $message = 'Success'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource->items(),
            'meta' => [
                'current_page' => $resource->currentPage(),
                'last_page' => $resource->lastPage(),
                'per_page' => $resource->perPage(),
                'total' => $resource->total(),
                'from' => $resource->firstItem(),
                'to' => $resource->lastItem(),
            ],
        ]);
    }

    /**
     * Réponse de ressource créée
     */
    protected function created(
        mixed $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return $this->success($data, $message, 201);
    }

    /**
     * Réponse de ressource mise à jour
     */
    protected function updated(
        mixed $data = null,
        string $message = 'Resource updated successfully'
    ): JsonResponse {
        return $this->success($data, $message, 200);
    }

    /**
     * Réponse de ressource supprimée
     */
    protected function deleted(
        string $message = 'Resource deleted successfully'
    ): JsonResponse {
        return $this->success(null, $message, 200);
    }

    /**
     * Réponse non autorisée
     */
    protected function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return $this->error($message, 401);
    }

    /**
     * Réponse interdite
     */
    protected function forbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return $this->error($message, 403);
    }

    /**
     * Réponse non trouvée
     */
    protected function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return $this->error($message, 404);
    }

    /**
     * Réponse d'erreur de validation
     */
    protected function validationError(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return $this->error($message, 422, $errors);
    }
}