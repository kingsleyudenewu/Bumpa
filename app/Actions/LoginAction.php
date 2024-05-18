<?php

namespace App\Actions;

use App\Models\User;

class LoginAction
{
    public function execute(array $data)
    {
        $user = User::where('email', $data['name'])->first();

        return (new GenerateTokenAction())->execute($user);
    }
}
