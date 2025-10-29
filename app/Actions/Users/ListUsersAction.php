<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ListUsersAction
{
    public function __invoke(): Collection
    {
        return User::query()->latest()->get();
    }
}
