<section class="trivento-past-editions py-16 bg-gray-50">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between items-end mb-10 border-b border-gray-200">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 border-b-4 border-pink-600 pb-1">
                Edições Anteriores
            </h2>

            <a href="{{ route('edicoes') }}" class="text-pink-600 hover:text-pink-800 font-semibold flex items-center transition pb-1">Ver todas →</a>
        </div>

        <div class="flex space-x-4 sm:space-x-6 overflow-x-auto pb-4 custom-scrollbar">

            @php $editions = [12, 11, 10, 9, 8, 7]; @endphp

            @foreach ($editions as $edition)
            <div class="edition-item flex-shrink-0 text-center p-4 border-2 border-gray-200 rounded-xl shadow-lg w-28 sm:w-32 hover:border-pink-500 hover:shadow-xl transition duration-300 cursor-pointer bg-white"
                onclick="window.location.href='{{ route('edicoes') }}'">

                <p class="text-4xl sm:text-5xl font-black text-gray-800 mb-1">{{ $edition }}</p>
                <p class="text-sm font-semibold text-pink-600">Edição</p>
                <p class="text-xs text-gray-500">2024</p>

            </div>
            @endforeach

        </div>

    </div>
</section>
