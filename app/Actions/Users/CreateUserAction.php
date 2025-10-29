<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    /**
     * @param array{name:string,email:string,password:string} $payload
     */
    public function __invoke(array $payload): User
    {
        $payload['password'] = Hash::make($payload['password']);

        return User::create($payload);
    }
}
