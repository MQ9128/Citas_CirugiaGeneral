<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Para peticiones Inertia
        if ($request->hasHeader('X-Inertia')) {
            return redirect()->intended('/admin/dashboard');
        }

        // Para peticiones normales
        return redirect('/admin/dashboard');
    }
}