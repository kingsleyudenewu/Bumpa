<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateTransaction
{
    public function execute(Request $request)
    {
        if (! $request->filled('user_pin') || ! is_string($request->user_pin)) {
            abort_if(Response::HTTP_BAD_REQUEST, 'Your transaction pin is required to be passed as part of the request payload with the parameter `user_pin`');
        }

        if ($this->validatePin($request, $request->user())) {
            return;
        }

        abort(Response::HTTP_BAD_REQUEST, 'Could not authenticate transaction');
    }

    /**
     * Validate the user's transaction pin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     *
     * @return bool
     */
    private function validatePin($request, $user): bool
    {
        return strlen($request->user_pin) === 6
            && Hash::check($request->user_pin, $user->pin);
    }
}
