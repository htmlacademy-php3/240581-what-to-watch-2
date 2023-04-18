<?php

namespace App\Services;

use App\Models\User;

/**
 * Прикладной сервис для объектов класса User
 *
 * @param  User $user - объект класса User
 * @return array
 */
class UserService
{
    public function __construct(
        private User $user,
    ) {
    }

    /**
     * Метод получения профиля пользователя
     *
     * @return array - массив с данными профиля пользователя
     */
    public function getProfileUser(): array
    {
        return [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->file,
            'role' => self::getRole(),
        ];
    }

    /**
     * Получение роли пользователя.
     *
     * @param User $user - одель класса User
     *
     * @return string - роль пользователя
     */
    public function getRole(): string
    {
        if ($this->user->is_moderator) {
            return User::ROLE_MODERATOR;
        }
        return User::ROLE_USER;
    }
}
