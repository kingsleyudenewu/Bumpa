<?php

namespace App\Actions;

use App\Models\User;

class GenerateTokenAction
{
    /**
     * Generate a new token for the user.
     *
     * @param User $user
     *
     * @return array
     */
    public function execute(User $user): array
    {
        $token = $user->createToken("API TOKEN")->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
            'token_type' => 'Bearer',
        ];
    }
}
