<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()), // Random password since Google auth
                    'email_verified_at' => now(),
                ]);

                // Assign 'Autor' role
                $autorRole = Role::where('name', 'Autor')->first();
                if ($autorRole) {
                    $user->assignRole($autorRole);
                }
            }

            Auth::login($user);

            // Redirect based on role
            if ($user->hasRole('Admin')) {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            } elseif ($user->hasRole('Coordenador')) {
                return redirect()->intended(route('coordenador.dashboard', absolute: false));
            } elseif ($user->hasRole('Revisor')) {
                return redirect()->intended(route('revisor.dashboard', absolute: false));
            } elseif ($user->hasRole('Autor')) {
                return redirect()->intended(route('autor.dashboard', absolute: false));
            }

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['google' => 'Erro ao fazer login com Google.']);
        }
    }
}
