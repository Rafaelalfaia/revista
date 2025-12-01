<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $mode = $request->input('login_mode', 'email');

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        if ($mode === 'cpf') {
            $rules['cpf'] = [
                'required',
                'string',
                'regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
                'unique:users,cpf',
            ];
        } else {
            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ];
        }

        $data = $request->validate($rules);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $mode === 'email' ? $data['email'] ?? null : null,
            'cpf'      => $mode === 'cpf' ? $data['cpf'] ?? null : null, // precisa ter coluna cpf em users
            'password' => Hash::make($data['password']),
        ]);



        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
