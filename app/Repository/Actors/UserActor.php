<?php

namespace App\Repository\Actors;

use App\Repository\Contracts\Repository;
use App\Models\User;

class UserActor extends Repository
{

    public function __construct(User $user)
    {
        $this->model = $user;
    }
}