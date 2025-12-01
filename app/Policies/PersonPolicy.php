<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\User;

class PersonPolicy
{
    /**
     * Voir une personne
     */
    public function view(User $user, Person $person): bool
    {
        return $user->id === $person->user_id;
    }

    /**
     * Mettre à jour une personne
     */
    public function update(User $user, Person $person): bool
    {
        return $user->id === $person->user_id;
    }

    /**
     * Supprimer une personne
     */
    public function delete(User $user, Person $person): bool
    {
        return $user->id === $person->user_id;
    }
}