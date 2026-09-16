<?php

use Illuminate\Support\Facades\Crypt;

if (! function_exists('encId')) {
    function encId($id): string
    {
        return Crypt::encryptString((string) $id);
    }
}

if (! function_exists('decId')) {
    function decId($value)
    {
        try {
            $dec = Crypt::decryptString($value);
            // Return as is — callers can cast to int if needed
            return $dec;
        } catch (\Throwable $e) {
            abort(404);
        }
    }
}

if (! function_exists('encIdForUrl')) {
    // Alias for blade convenience — same as encId (route() will urlencode)
    function encIdForUrl($id): string
    {
        return encId($id);
    }
}
