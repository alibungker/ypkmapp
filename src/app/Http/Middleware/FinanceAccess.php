<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FinanceAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user || !$user->canViewKeuangan()) {
            abort(403, 'Akses khusus pengelola keuangan.');
        }
        return $next($request);
    }
}
