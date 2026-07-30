<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OperationalOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->isRelawan())) {
            abort(403, 'Akses khusus Admin atau Relawan YPKM.');
        }

        return $next($request);
    }
}
