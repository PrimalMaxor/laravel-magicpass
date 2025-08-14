<?php

if (!function_exists('magicpass_url')) {
    /**
     * Generate a magic pass login URL
     */
    function magicpass_url($code): mixed
    {
        return url('/magicpass/login?code=' . $code);
    }
} 