<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Person extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'notes',
        'face_token',
        'face_age',
        'face_gender',
        'image_path',
        'image_original_name',
        'is_active',
        'registered_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'registered_at' => 'datetime',
    ];

    protected $appends = [
        'full_name',
        'image_url',
    ];

    /**
     * Nom complet de la personne
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * URL publique de l'image
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    /**
     * Scope : Personnes actives uniquement
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope : Recherche par nom
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Utilisateur qui a créé cette personne
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifications associées à cette personne
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(FaceVerification::class);
    }

    /**
     * Supprimer l'image du storage lors de la suppression
     */
    protected static function booted()
    {
        static::deleting(function ($person) {
            if ($person->image_path && Storage::exists($person->image_path)) {
                Storage::delete($person->image_path);
            }
        });
    }
}