<?php

namespace App\services;

use \App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Прикладной сервис PermissionCheckService
 * управления доступом на основе ролей и прав
 *
 */
class PermissionCheckService
{
    /**
     * Проверка, что пользователь модератор
     *
     * @param  User $user
     * @return bool
     */
    private static function isModerator(User $user): bool
    {
        if (isset($user->is_moderator)) {
            return $user->is_moderator === true;
        }
        return false;
    }

    /**
     * Проверка, что пользователь автор ресурса
     *
     * @param  $resource - ресурс, авторство, которого проверяется
     * @return bool
     */
    private static function isAuthor($resource): bool
    {
        if (isset($resource->user->id)) {
            return $resource->user->id === Auth::user()->id;
        }

        if (isset($resource->user_id)) {
            return $resource->user_id === Auth::user()->id;
        }
        return false;
    }

    /**
     * Проверка прав модератора и автора
     *
     * @param  $resource - ресурс, авторство, которого проверяется
     * @param  User $user
     * @return bool
     */
    public static function checkPermission($resource = null, User $user = null): bool
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (self::isModerator($user)) {
            return true;
        }

        if (self::isAuthor($resource)) {
            return true;
        }
        return false;
    }
}
