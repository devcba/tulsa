<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    /**
     * @param array{name?:string,email?:string,password?:string} $payload
     */
    public function __invoke(User $user, array $payload): User
    {
        if (array_key_exists('password', $payload)) {
            $payload['password'] = Hash::make($payload['password']);
        }

        $user->fill($payload);
        $user->save();

        return $user->fresh();
    }
}
