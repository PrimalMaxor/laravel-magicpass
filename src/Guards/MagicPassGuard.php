<?php

namespace Primalmaxor\MagicPass\Guards;

use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MagicPassGuard extends SessionGuard
{
    public function __construct($name, UserProvider $provider, Session $session, Request $request = null)
    {
        parent::__construct($name, $provider, $session, $request);
    }

    /**
     * Validate a user's credentials without password verification
     */
    public function validate(array $credentials = [])
    {
        if (isset($credentials['email'])) {
            $user = $this->provider->retrieveByCredentials(['email' => $credentials['email']]);
            return $user !== null;
        }
        
        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials without password
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        return false;
    }

    /**
     * Login a user without password verification
     */
    public function loginWithoutPassword($user, $remember = false)
    {
        $this->setUser($user);
        
        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);
        }
        
        $this->fireEvent('login', [$user, $remember]);
        
        return true;
    }
} 