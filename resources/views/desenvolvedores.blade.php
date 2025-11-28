<x-guest-layout> 
    {{-- Container Principal: Fundo Cinza Claro no Light Mode e Fundo Escuro no Dark Mode --}}
    <div class="min-h-screen flex flex-col items-center py-12 bg-[#E4E4E7] dark:bg-[#18181B] transition duration-500">
        
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Seção de Título e Mensagem de Agradecimento --}}
            <header class="text-center mb-12 pt-8">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-[#DB1F5D] mb-4 tracking-tight transition duration-500">
                    A Equipe de Criação
                </h1>
                <p class="text-lg sm:text-xl text-[#71717A] dark:text-[#A1A1AA] max-w-3xl mx-auto transition duration-500">
                    A Revista Científica Trivento Educação foi desenvolvida com dedicação e paixão por talentosos alunos, em colaboração com a faculdade. Nosso sincero agradecimento por esta belíssima plataforma!
                </p>
            </header>

            {{-- Grid de Perfis dos Desenvolvedores: Alterado para FLEXBOX para CENTRALIZAÇÃO --}}
            <div class="flex flex-wrap justify-center gap-6 sm:gap-8">

                @foreach($developers as $dev)
                <div class="w-full sm:max-w-[45%] md:max-w-[30%] lg:max-w-[22.5%] bg-white dark:bg-[#27272A] shadow-xl shadow-[#71717A]/20 dark:shadow-black/50 rounded-2xl overflow-hidden transform hover:scale-[1.05] transition duration-300 ease-in-out border border-[#D4D4D8]/50 dark:border-[#3F3F46] p-6 text-center">
                    <img class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-[#DB1F5D] object-cover" src="{{ $dev->avatar ?? '/images/avatar.png' }}" alt="{{ $dev->name }}">
                    <h4 class="text-xl font-bold text-[#000000] dark:text-[#FAFAFA] mb-1 transition duration-500">{{ $dev->name }}</h4>
                    <p class="text-sm text-white bg-[#DB1F5D] inline-block px-3 py-1 rounded-full mb-3 font-medium">{{ $dev->role }}</p>
                    @if(!empty($dev->bio))<p class="text-sm text-[#71717A] dark:text-[#A1A1AA] italic transition duration-500">{{ $dev->bio }}</p>@endif
                    <div class="mt-4 flex justify-center space-x-4">
                        @if(!empty($dev->linkedin))
                        <a href="{{ $dev->linkedin }}" class="text-[#71717A] hover:text-[#DB1F5D] dark:text-[#A1A1AA] dark:hover:text-[#DB1F5D] transition duration-200" target="_blank" rel="noopener noreferrer" title="LinkedIn">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.76 0-5 2.24-5 5v14c0 2.76 2.24 5 5 5h14c2.76 0 5-2.24 5-5v-14c0-2.76-2.24-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.784 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.366-4-3.284-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.778 7 2.45v6.785z"/></svg>
                        </a>
                        @endif
                        @if(!empty($dev->github))
                        <a href="{{ $dev->github }}" class="text-[#71717A] hover:text-[#DB1F5D] dark:text-[#A1A1AA] dark:hover:text-[#DB1F5D] transition duration-200" target="_blank" rel="noopener noreferrer" title="GitHub">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.372 0 0 5.373 0 12a12 12 0 008.208 11.385c.6.11.793-.26.793-.577v-2.165c-3.338.726-4.042-1.61-4.042-1.61-.546-1.388-1.334-1.758-1.334-1.758-1.09-.744.082-.729.082-.729 1.205.084 1.84 1.236 1.84 1.236 1.07 1.835 2.807 1.305 3.492.997.107-.775.42-1.305.763-1.605-2.665-.3-5.466-1.337-5.466-5.934 0-1.31.468-2.382 1.235-3.223-.123-.301-.535-1.51.117-3.15 0 0 1.006-.322 3.3 1.23a11.5 11.5 0 012.998-.402c1.02.005 2.04.138 2.998.402 2.292-1.552 3.296-1.23 3.296-1.23.655 1.64.244 2.85.12 3.15.77.84 1.233 1.91 1.233 3.22 0 4.61-2.807 5.63-5.48 5.932.43.37.82 1.096.82 2.21v3.28c0 .32.195.694.8.57A12 12 0 0024 12c0-6.627-5.373-12-12-12z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
                
            </div>
            
            {{-- Seção de Créditos Finais --}}
            <div class="mt-16 text-center text-[#71717A] dark:text-[#A1A1AA] transition duration-500">
                <p>&copy; {{ date('Y') }} Revista Científica Trivento Educação. Plataforma desenvolvida com 💖.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
