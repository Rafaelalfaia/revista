<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden" style="background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-gradient-to-br from-[#DB1F5D]/10 via-transparent to-[#DB1F5D]/5 animate-pulse"></div>
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-[#DB1F5D]/5 rounded-full blur-3xl animate-bounce" style="animation-duration: 8s;"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-[#71717A]/5 rounded-full blur-3xl animate-bounce" style="animation-duration: 10s; animation-delay: 2s;"></div>
        </div>

        <!-- Login Card -->
        <div class="relative z-10 w-full max-w-md mx-4">
            <div class="bg-white/95 backdrop-blur-sm shadow-2xl rounded-2xl p-8 border border-[#D4D4D8]/20">
                <!-- Logo -->
                <div class="text-center mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="Revista Trivento" class="w-24 h-24 mx-auto mb-4 drop-shadow-lg">
                    <h1 class="text-3xl font-bold text-[#000000] mb-2">Revista Trivento</h1>
                    <p class="text-[#71717A] text-lg">Acesse sua conta</p>
                </div>

                <!-- Google Login Button -->
                <div class="mb-6">
                    <a href="{{ route('auth.google') }}" class="w-full flex justify-center items-center px-6 py-3 border-2 border-[#D4D4D8] rounded-xl shadow-sm text-base font-medium text-[#000000] bg-white hover:bg-[#E4E4E7] transition-all duration-200 ease-in-out transform hover:scale-105 hover:shadow-lg">
                        <svg class="w-6 h-6 mr-3" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        Entrar com Google
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[#D4D4D8]"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-[#71717A] font-medium">Ou entre com e-mail</span>
                    </div>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('E-mail')" class="text-[#000000] font-semibold" />
                        <x-text-input id="email" class="block mt-2 w-full border-[#D4D4D8] focus:border-[#DB1F5D] focus:ring-[#DB1F5D] rounded-lg" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Senha')" class="text-[#000000] font-semibold" />

                        <x-text-input id="password" class="block mt-2 w-full border-[#D4D4D8] focus:border-[#DB1F5D] focus:ring-[#DB1F5D] rounded-lg"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-[#D4D4D8] text-[#DB1F5D] shadow-sm focus:ring-[#DB1F5D] focus:ring-offset-0" name="remember">
                        <label for="remember_me" class="ml-3 text-sm text-[#71717A] font-medium">
                            {{ __('Lembrar-me') }}
                        </label>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#71717A] hover:text-[#DB1F5D] font-medium transition-colors duration-200" href="{{ route('password.request') }}">
                                {{ __('Esqueceu a senha?') }}
                            </a>
                        @endif

                        <button type="submit" class="bg-[#DB1F5D] hover:bg-[#B91C4D] text-white font-bold py-3 px-8 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#DB1F5D] focus:ring-offset-2">
                            {{ __('Entrar') }}
                        </button>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <a class="text-[#71717A] hover:text-[#DB1F5D] font-medium transition-colors duration-200" href="{{ route('register') }}">
                        {{ __('Não tem conta? Cadastre-se') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</x-guest-layout>
