<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Traits\TraitReturnJsonOlirum;

class RateLimitLogin
{
    use TraitReturnJsonOlirum;

    protected int $maxAttempts = 5;
    protected int $decaySeconds = 60;

    public function handle(Request $request, Closure $next)
    {
        $key = $this->resolveKey($request);

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return $this->returnJson(
                null,
                "Muitas tentativas. Tente novamente em {$seconds} segundos.",
                false,
                429
            );
        }

        RateLimiter::hit($key, $this->decaySeconds);

        return $next($request);
    }

    protected function resolveKey(Request $request): string
    {
        $email = Str::lower($request->input('email', ''));
        return 'login|' . $email . '|' . $request->ip();
    }
}
