<x-guest-layout>
    {{-- Main Container: Fundo Cinza Claro e Profissional --}}
    <div class="min-h-screen flex items-center justify-center relative overflow-hidden bg-[#E4E4E7]">
        
        {{-- Custom Animated Background Layer (Muito sutil, quase invisível no Light Mode) --}}
        <div class="absolute inset-0">
            {{-- Gentle light source 1: Pink (flutuação sutil, em tom pastel no fundo claro) --}}
            <div class="absolute top-[-10rem] left-[-10rem] w-[30rem] h-[30rem] bg-[#DB1F5D]/10 rounded-full blur-3xl opacity-30 animate-float" style="animation-duration: 12s;"></div>
            {{-- Gentle light source 2: Gray (flutuação sutil) --}}
            <div class="absolute bottom-[-15rem] right-[-15rem] w-[40rem] h-[40rem] bg-[#71717A]/10 rounded-full blur-3xl opacity-20 animate-float" style="animation-delay: 6s; animation-duration: 15s;"></div>
        </div>

        {{-- Login Card: Card Branco, Centralizado, com BORDAS E SOMBRA SUAVE --}}
        <div class="relative z-10 w-full max-w-lg mx-4 sm:mx-6 lg:mx-4">
            {{-- ADICIONADO: rounded-2xl e shadow-2xl shadow-[#71717A]/30 --}}
            <div class="bg-white shadow-2xl shadow-[#71717A]/30 rounded-2xl p-6 sm:p-8 lg:p-12 border border-[#D4D4D8]/50 transition-all duration-300 ease-out">

                {{-- Logo e Título --}}
                <div class="text-center mb-6 sm:mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="Revista Trivento" class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 mx-auto mb-4 drop-shadow-md">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#000000] mb-2 tracking-tight">Revista Trivento</h1>
                    <p class="text-[#71717A] text-base sm:text-lg font-light">Acesse sua conta para continuar</p>
                </div>

                {{-- Google Login Button --}}
                <div class="mb-6">
                    <a href="{{ route('auth.google') }}" class="w-full flex justify-center items-center px-4 sm:px-6 py-2 sm:py-3 border border-[#D4D4D8] rounded-lg shadow-sm text-sm sm:text-base font-medium text-[#000000] bg-white hover:bg-[#E4E4E7] active:bg-[#D4D4D8] transition-all duration-200 ease-in-out">
                        {{-- SVG do Google --}}
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 sm:mr-3" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span class="hidden sm:inline">Entrar com Google</span>
                        <span class="sm:hidden">Google</span>
                    </a>
                </div>

                {{-- Divider --}}
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-[#D4D4D8]"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-[#71717A] font-light">ou continue com seu e-mail</span>
                    </div>
                </div>

                {{-- Session Status --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email Address --}}
                    <div>
                        <x-input-label for="email" :value="__('E-mail')" class="text-[#000000] font-semibold" />
                        <x-text-input id="email" class="block mt-1 w-full border-[#D4D4D8] focus:border-[#DB1F5D] focus:ring-[#DB1F5D] rounded-lg text-[#000000] transition duration-150" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Senha')" class="text-[#000000] font-semibold" />
                        <x-text-input id="password" class="block mt-1 w-full border-[#D4D4D8] focus:border-[#DB1F5D] focus:ring-[#DB1F5D] rounded-lg text-[#000000] transition duration-150"
                                     type="password"
                                     name="password"
                                     required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    {{-- Remember Me and Forgot Password --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pt-2 space-y-2 sm:space-y-0">
                        {{-- Remember Me --}}
                        <div class="flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-[#D4D4D8] text-[#DB1F5D] shadow-sm focus:ring-[#DB1F5D] focus:ring-offset-0 transition duration-150" name="remember">
                            <label for="remember_me" class="ml-2 text-sm text-[#71717A] font-medium">
                                {{ __('Lembrar-me') }}
                            </label>
                        </div>

                        {{-- Forgot Password --}}
                        @if (Route::has('password.request'))
                            <a class="text-sm text-[#71717A] hover:text-[#DB1F5D] font-medium transition-colors duration-200" href="{{ route('password.request') }}">
                                {{ __('Esqueceu a senha?') }}
                            </a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-[#DB1F5D] hover:bg-[#B91C4D] text-white font-bold py-2 sm:py-3 rounded-lg transition-all duration-300 transform hover:scale-[1.01] shadow-lg shadow-[#DB1F5D]/30 focus:outline-none focus:ring-2 focus:ring-[#DB1F5D] focus:ring-offset-2">
                            <span class="text-sm sm:text-base">{{ __('Entrar na Plataforma') }}</span>
                        </button>
                    </div>
                </form>

                {{-- Register Link --}}
                <div class="mt-6 text-center">
                    <p class="text-sm text-[#71717A] font-light">
                        Não tem conta?
                        <a class="text-[#DB1F5D] hover:text-[#B91C4D] font-medium transition-colors duration-200" href="{{ route('register') }}">
                            Cadastre-se para Publicar
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Custom CSS for the elegant float animation --}}
    <style>
        @keyframes float {
            0% { transform: translate(0, 0); }
            25% { transform: translate(10px, 15px); }
            50% { transform: translate(-10px, -5px); }
            75% { transform: translate(5px, 10px); }
            100% { transform: translate(0, 0); }
        }

        .animate-float {
            animation: float linear infinite;
        }
    </style>
</x-guest-layout>