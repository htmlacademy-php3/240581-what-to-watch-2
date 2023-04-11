<?php

namespace App\Policies;

use App\Models\Film;
use App\Models\User;
use \Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class FilmPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->is_moderator;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Film  $film
     * @return Response|bool
     */
    public function update(User $user): Response|bool
    {
        return $user->is_moderator;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Film  $film
     * @return Response|bool
     */
    public function delete(User $user): Response|bool
    {
        return $user->is_moderator;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Film  $film
     * @return Response|bool
     */
    public function restore(User $user): Response|bool
    {
        return $user->is_moderator;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Film  $film
     * @return Response|bool
     */
    public function forceDelete(User $user): Response|bool
    {
        return $user->is_moderator;
    }
}
