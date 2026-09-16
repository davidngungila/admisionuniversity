<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class DecryptRouteIds
{
    /**
     * Route param names that should be treated as encrypted IDs.
     * If a param name is not listed, we still try to decrypt generically when value looks encrypted.
     */
    protected array $encryptedParams = [
        'application',
        'programme',
        'window',
        'academicYear',
        'academic_year',
        'academicYearId',
        'round',
        'workflow',
        'campus',
        'faculty',
        'department',
        'applicant',
        'payment',
        'document',
        'batch',
        'result',
        'setting',
        'user',
        'letter',
        'admissionWindow',
        'applicationRound',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if ($route) {
            foreach ($route->parameters() as $name => $value) {
                if (! is_string($value)) {
                    continue;
                }
                if (is_numeric($value) && strlen($value) < 10) {
                    continue;
                }
                try {
                    $decrypted = Crypt::decryptString($value);
                    if (is_numeric($decrypted) || ctype_digit((string) $decrypted)) {
                        \Illuminate\Support\Facades\Log::info("DecryptRouteIds: $name $value -> $decrypted for ".$request->path());
                        $route->setParameter($name, $decrypted);
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::info("DecryptRouteIds failed for $name=$value : ".$e->getMessage());
                }
            }
        }

        return $next($request);
    }
}
