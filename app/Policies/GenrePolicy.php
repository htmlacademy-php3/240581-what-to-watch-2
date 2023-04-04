<?php

namespace App\Policies;

use App\Models\User;
use \Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class GenrePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Genre  $genre
     * @return Response|bool
     */
    public function update(User $user): Response|bool
    {
        return $user->is_moderator;
    }
}
