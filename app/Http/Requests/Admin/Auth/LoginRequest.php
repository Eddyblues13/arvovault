<?php

namespace App\Http\Requests\Admin\Auth;

use App\Http\Requests\Auth\LoginRequest as UserLoginRequest;

class LoginRequest extends UserLoginRequest
{
    /**
     * Get the rate limiting key for the request.
     *
     * Admin attempts are counted separately, so failed admin sign-ins never lock
     * the same email address out of the user sign-in, or the other way around.
     */
    public function throttleKey(): string
    {
        return 'admin|'.parent::throttleKey();
    }

    /**
     * Get the name of the authentication guard that checks the credentials.
     */
    protected function guardName(): string
    {
        return 'admin';
    }
}
