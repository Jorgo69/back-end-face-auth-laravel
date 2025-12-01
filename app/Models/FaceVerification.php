<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceVerification extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'person_id',
        'type',
        'match_found',
        'status',
        'detail',
        'ip_address',
        'user_agent',
        'metadata',
        'response_time_ms',
    ];

    protected $casts = [
        'match_found' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Types de vérification
     */
    const TYPE_USER_LOGIN = 'user_login';
    const TYPE_PERSON_MATCH = 'person_match';
    const TYPE_TWO_IMAGES = 'two_images';
    const TYPE_ENROLLMENT = 'enrollment';

    /**
     * Statuts
     */
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';
    const STATUS_ERROR = 'error';

    /**
     * Scope : Vérifications réussies
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS)
                     ->where('match_found', true);
    }

    /**
     * Scope : Vérifications échouées
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED)
                     ->orWhere('match_found', false);
    }

    /**
     * Scope : Par type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : Dernières 24h
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDay());
    }

    /**
     * Utilisateur concerné
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Personne concernée
     */
    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    /**
     * Vérifier si la vérification a réussi
     */
    public function isSuccessful(): bool
    {
        return $this->status === self::STATUS_SUCCESS && $this->match_found;
    }

    /**
     * Créer une vérification
     */
    public static function createVerification(array $data): self
    {
        return self::create([
            'user_id' => $data['user_id'] ?? null,
            'person_id' => $data['person_id'] ?? null,
            'type' => $data['type'],
            'match_found' => $data['match_found'] ?? false,
            'status' => $data['status'] ?? self::STATUS_PENDING,
            'detail' => $data['detail'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $data['metadata'] ?? null,
            'response_time_ms' => $data['response_time_ms'] ?? null,
        ]);
    }
}