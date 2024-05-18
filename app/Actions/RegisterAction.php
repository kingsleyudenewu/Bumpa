<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createUser($data['name'], $data['email'], $data['password']);

            return (new GenerateTokenAction())->execute($user);
        });
    }

    private function createUser(string $name, string $email, string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);
    }
}
