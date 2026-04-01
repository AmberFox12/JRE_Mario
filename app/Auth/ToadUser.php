<?php

namespace App\Auth;

use Illuminate\Auth\GenericUser;

class ToadUser extends GenericUser
{
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value) {}

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}