<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MagicPass Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the MagicPass package.
    | All settings can be customized after publishing the config file.
    |
    */

    // Code expiry time in minutes
    'expiry' => env('MAGICPASS_EXPIRY', 15),
    
    // Length of the generated code
    'code_length' => env('MAGICPASS_CODE_LENGTH', 4),
    
    // Maximum verification attempts before throttling
    'max_attempts' => env('MAGICPASS_MAX_ATTEMPTS', 3),
    
    // Throttle time in minutes for failed attempts
    'throttle_minutes' => env('MAGICPASS_THROTTLE_MINUTES', 15),
    
    // Auto-create users if they don't exist
    'auto_create_users' => env('MAGICPASS_AUTO_CREATE_USERS', true),
    
    // Auto-verify email addresses
    'auto_verify_email' => env('MAGICPASS_AUTO_VERIFY_EMAIL', true),
    
    // Route prefix for all MagicPass routes
    'route_prefix' => env('MAGICPASS_ROUTE_PREFIX', 'magicpass'),
    
    // Redirect after successful login
    'redirect_after_login' => env('MAGICPASS_REDIRECT_AFTER_LOGIN', '/'),
    
    // Redirect after logout
    'redirect_after_logout' => env('MAGICPASS_REDIRECT_AFTER_LOGOUT', '/magicpass/login'),
    
    // Enable/disable CSRF protection
    'csrf_protection' => env('MAGICPASS_CSRF_PROTECTION', true),
    
    // Enable/disable rate limiting
    'rate_limiting' => env('MAGICPASS_RATE_LIMITING', true),
    
    // Rate limit attempts per minute
    'rate_limit_per_minute' => env('MAGICPASS_RATE_LIMIT_PER_MINUTE', 5),
];
