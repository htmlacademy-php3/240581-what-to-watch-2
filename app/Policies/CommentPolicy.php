<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use \Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Comment  $comment
     * @return Response|bool
     */
    public function update(User $user, Comment $comment): Response|bool
    {
        return ($user->is_moderator || $user->id === $comment->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Comment  $comment
     * @return Response|bool
     */
    public function delete(User $user, Comment $comment): Response|bool
    {
        return ($user->is_moderator || $user->id === $comment->user_id);
    }
}
