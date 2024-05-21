<?php

namespace App\Actions;

use App\Enums\BookEnum;
use App\Models\Book;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterAction
{
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createUser($data['name'], $data['email'], $data['password']);

            $this->createUserLedger($user);

            return (new GenerateTokenAction())->execute($user);
        });
    }

    /**
     * Create a user
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return User
     */
    private function createUser(string $name, string $email, string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Create a user ledger
     *
     * @param User $user
     * @return Book
     */
    private function createUserLedger(User $user)
    {
        return Book::create([
            'book_src_id' => $user->id,
            'book_type' => BookEnum::CUSTOMER
        ]);
    }
}
