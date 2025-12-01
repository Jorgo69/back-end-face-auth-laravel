<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'face_token',
        'face_age',
        'face_gender',
        'face_registered_at',
        'face_last_verified_at',
        'face_verification_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'face_token', // Ne JAMAIS exposer le token dans l'API
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'face_registered_at' => 'datetime',
            'face_last_verified_at' => 'datetime',
            'password' => 'hashed',
        ];   
    }

    /**
     * Vérifier si l'utilisateur a enregistré son visage
     */
    public function hasFaceRegistered(): bool
    {
        return !is_null($this->face_token);
    }

    /**
     * Incrémenter le compteur de vérifications
     */
    public function incrementFaceVerificationCount(): void
    {
        $this->increment('face_verification_count');
        $this->update(['face_last_verified_at' => now()]);
    }

    /**
     * Personnes créées par cet utilisateur
     */
    public function persons(): HasMany
    {
        return $this->hasMany(Person::class);
    }

    /**
     * Vérifications faciales
     */
    public function faceVerifications(): HasMany
    {
        return $this->hasMany(FaceVerification::class);
    }
}
