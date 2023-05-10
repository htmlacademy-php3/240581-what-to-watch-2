<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Storage;

/**
 * Прикладной сервис для объектов класса User
 *
 * @param  User $user - объект класса User
 *
 */
class UserService
{
    public function __construct(
        private User $user
    ) {
    }

    /**
     * Обновление профиля пользователя.
     *
     * @param  UpdateUserRequest $request
     * @param  User $user - одель класса User
     *
     * @return void
     */
    public function updateUser(UpdateUserRequest $request, User $user): void
    {
        $params = $request->toArray();

        if (isset($params['password'])) {
            $user->password = Hash::make($params['password']);
        }

        if (isset($user->file)) {
            $oldPath = $user->file;
        }

        if ($request->hasFile('file')) {
            $params['file'] = $request->file('file');
            $user->file = $params['file']->store('avatars');
        }

        $user->name = $params['name'];
        $user->email = $params['email'];

        if ($user->isDirty()) {
            $user->save();
        }

        Storage::delete($oldPath);
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
