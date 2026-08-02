<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ProfileOwner
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) abort(403, 'Harus login.');
        // Hanya bisa edit profil sendiri
        if ($request->route('user') && (int) $request->route('user')->id !== (int) $user->id) {
            abort(403, 'Hanya bisa mengubah profil sendiri.');
        }
        return $next($request);
    }
}
