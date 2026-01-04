<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Default to app timezone
            $timezone = config('app.timezone');

            // Check if user has doctor profile with timezone
            if ($user->doctorProfile && $user->doctorProfile->timezone) {
                $timezone = $user->doctorProfile->timezone;
            }

            // Set a custom config for display timezone, BUT keep app.timezone as UTC for storage integrity
            Config::set('app.display_timezone', $timezone);

            // Note: We do NOT change date_default_timezone_set or app.timezone globally to avoid
            // messing up database storage (which should be UTC).
            // Filament components will be configured to use 'app.display_timezone'.

            // Log for monitoring (as per requirement: Sistem logging yang mencatat aktivitas terkait time zone)
            // We don't want to log on every request in production, but for debugging/verification:
            // Log::debug("App timezone set to {$timezone} for user {$user->id}");
        }

        return $next($request);
    }
}
