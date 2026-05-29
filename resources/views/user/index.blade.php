@extends('layouts.app')

@section('content')
    <!-- Hero Section Container -->
    <section class="px-3 sm:px-10 md:px-16 lg:px-24 py-8 md:py-10">
        <div class="max-w-8xl mx-auto flex flex-col lg:flex-row items-center lg:items-start gap-8 lg:gap-10">

            <div class="flex-1 w-full text-center lg:text-left">

                <span
                    class="inline-block bg-brand-gradient text-white px-4 py-1.5 rounded-md text-xs sm:text-sm font-bold shadow-sm mb-4">
                    Sistem Pickup Order PNC
                </span>

                <h1 class="text-4xl sm:text-5xl font-bold leading-tight text-base-content">
                    Laper Abis
                    <br class="block sm:hidden" />
                    <span id="typing-text" class="text-emerald-400">Nugas?</span>
                </h1>

                <h2 class="text-xl sm:text-3xl font-semibold mb-3 text-base-content leading-relaxed">
                    Langsung Order Makanan
                    <br class="block md:hidden" />
                    <span
                        class="inline-block bg-emerald-400 text-white px-4 py-1.5 sm:py-1 rounded-lg text-lg md:text-3xl font-semibold mt-2 md:mt-0 md:ml-1 shadow-sm">
                        Tanpa Perlu Ke Kantin
                    </span>
                </h2>

                <p
                    class="text-sm sm:text-base text-base-content/75 font-medium max-w-md mx-auto lg:mx-0 mb-6 leading-relaxed">
                    Platform pemesanan makanan kampus yang cepat, efisien, dan dirancang khusus untuk civitas akademik
                    Politeknik Negeri Cilacap.
                </p>

                <!-- Integrated Search Bar Widget -->
                <form action="{{ route('canteen.index') }}" method="GET" class="w-full max-w-md mx-auto lg:mx-0 mb-8">
                    <div class="relative w-full">
                        <label class="input input-bordered flex items-center w-full shadow-sm rounded-3xl input-md pr-12">
                            <input type="search" name="search" class="grow text-sm sm:text-base pl-2"
                                placeholder="Cari menu atau kantin..." />
                        </label>
                        <button type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Hero CTA Buttons -->
                <div class="flex flex-row gap-4 justify-center lg:justify-start items-center">
                    <a href="{{ route('canteen.index') }}"
                        class="btn bg-fern-700 text-white hover:bg-fern-800 border-none shadow-md hover:shadow-lg px-6 py-3 min-h-0 h-auto text-sm font-bold rounded-2xl w-auto transition-all duration-200 active:scale-95 flex items-center gap-2">
                        <span>Pesan Sekarang</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="#menu-populer"
                        class="btn bg-vanilla-custard-200 text-fern-900 hover:bg-vanilla-custard-300 border-none shadow-sm hover:shadow-md px-6 py-3 min-h-0 h-auto text-sm font-bold rounded-2xl w-auto transition-all duration-200 active:scale-95">
                        Lihat Menu
                    </a>
                </div>
            </div>

            <div class="flex-1 w-full flex justify-center lg:justify-end">
                <div
                    class="w-[350px] h-[350px] sm:w-[450px] sm:h-[450px] md:w-[520px] md:h-[520px] lg:w-[580px] lg:h-[580px] max-w-full overflow-hidden flex items-center justify-center">
                    <img src="{{ asset('assets/illustration/Eating%20healthy%20food-cuate%20(1).svg') }}"
                        alt="Ilustrasi Pesan Makanan" class="w-full h-full object-contain" />
                </div>
            </div>

        </div>
    </section>

    <!-- Shortcut Kategori Menu -->
    @if ($categories->isNotEmpty())
        <section class="px-3 sm:px-10 md:px-16 lg:px-24 pb-4">
            <div class="max-w-8xl mx-auto">
                <div class="mb-4 text-center sm:text-left">
                    <h2 class="text-xl sm:text-2xl font-bold text-base-content">Pilih Kategori</h2>
                </div>
                @php
                    $catConfig = [
                        'Makanan' => [
                            'bg' => 'bg-fern-50 hover:bg-fern-100 border-fern-200 text-fern-700',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4v3m0 0a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7zm-8 10h16a1 1 0 0 1 1 1v1H3v-1a1 1 0 0 1 1-1z"/>',
                        ],
                        'Minuman' => [
                            'bg' => 'bg-emerald-50 hover:bg-emerald-100 border-emerald-200 text-emerald-700',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-2M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8zM6 3v2m4-2v2m4-2v2"/>',
                        ],
                        'Camilan' => [
                            'bg' =>
                                'bg-vanilla-custard-100 hover:bg-vanilla-custard-200 border-vanilla-custard-300 text-vanilla-custard-800',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18z M9 9.5H9.01M15 11H15.01M11 15H11.01M14 15H14.01M8 12H8.01"/>',
                        ],
                    ];
                    $defaultCfg = [
                        'bg' => 'bg-base-200 hover:bg-base-300 border-base-300 text-base-content',
                        'icon' =>
                            '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>',
                    ];
                @endphp
                <div
                    class="flex items-center justify-center sm:justify-start gap-4 sm:gap-5 overflow-x-auto pb-3 scrollbar-hide -mx-3 px-3 sm:mx-0 sm:px-0">
                    {{-- Kategori Dinamis --}}
                    @foreach ($categories as $cat)
                        @php $cfg = $catConfig[$cat] ?? $defaultCfg; @endphp
                        <a href="{{ route('canteen.index', ['category' => $cat]) }}"
                            class="flex flex-col items-center gap-2 shrink-0 group">
                            <div
                                class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl border {{ $cfg['bg'] }} flex items-center justify-center shadow-sm group-hover:scale-105 group-hover:shadow-md transition-all duration-200 active:scale-95">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    {!! $cfg['icon'] !!}
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-base-content whitespace-nowrap">{{ $cat }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif


    <!-- Langkah Mudah Memesan Section -->

    <section class="px-3 sm:px-10 md:px-16 lg:px-24 py-10">
        <div class="max-w-8xl mx-auto">
            <div class="mb-8 text-center">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-base-content">Cara Pesan Makanan</h2>
                <p class="text-base-content/60 text-sm mt-2 font-medium">Mudah, cepat, tanpa perlu antri.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">

                <!-- Step 1 -->
                <div
                    class="bg-base-100 border border-base-200 rounded-3xl p-6 flex flex-row md:flex-col items-center md:items-center gap-5 md:gap-4 md:text-center shadow-sm hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="shrink-0 w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-fern-50 flex items-center justify-center text-fern-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-fern-700 uppercase tracking-wider">Langkah 1</span>
                        <h3 class="text-base font-bold text-base-content mt-0.5 mb-1">Pilih Menu</h3>
                        <p class="text-xs text-base-content/60 font-medium leading-snug">Jelajahi menu favorit dari kantin
                            kampus.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div
                    class="bg-base-100 border border-base-200 rounded-3xl p-6 flex flex-row md:flex-col items-center md:items-center gap-5 md:gap-4 md:text-center shadow-sm hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="shrink-0 w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-vanilla-custard-100 flex items-center justify-center text-fern-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-fern-700 uppercase tracking-wider">Langkah 2</span>
                        <h3 class="text-base font-bold text-base-content mt-0.5 mb-1">Bayar Pesanan</h3>
                        <p class="text-xs text-base-content/60 font-medium leading-snug">Selesaikan pembayaran tunai atau
                            online.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div
                    class="bg-base-100 border border-base-200 rounded-3xl p-6 flex flex-row md:flex-col items-center md:items-center gap-5 md:gap-4 md:text-center shadow-sm hover:-translate-y-1 transition-transform duration-300">
                    <div
                        class="shrink-0 w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-fern-700 uppercase tracking-wider">Langkah 3</span>
                        <h3 class="text-base font-bold text-base-content mt-0.5 mb-1">Ambil di Kantin</h3>
                        <p class="text-xs text-base-content/60 font-medium leading-snug">Ambil makananmu setelah pesanan
                            siap.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Menu Populer Section -->
    <section id="menu-populer" class="px-3 sm:px-10 md:px-16 lg:px-24 py-8">
        <div class="max-w-8xl mx-auto">

            <div class="mb-8 text-center sm:text-left">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-base-content">Menu Terpopuler</h2>
                <p class="text-base-content/60 text-sm mt-1.5 font-medium">Menu favorit civitas akademika PNC
                    yang paling sering dipesan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($popularMenus as $menu)
                    <x-foodcard :id="$menu->id" :name="$menu->name" :canteenName="$menu->canteen->name" :description="$menu->description" :price="$menu->formatted_price"
                        :image="$menu->image ? asset('storage/' . $menu->image) : null" :rating="number_format($menu->average_rating, 1)" :actionUrl="route('menu.show', ['canteenId' => $menu->canteen_id, 'id' => $menu->id])" />
                @empty
                    <div class="col-span-full p-8 text-center bg-vanilla-custard-50 border border-base-200 rounded-3xl">
                        <p class="text-base-content/60 font-medium">Belum ada menu populer.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    <!-- Pilih Kantin Section -->
    <section class="px-3 sm:px-10 md:px-16 lg:px-24 py-8 mb-10">
        <div class="max-w-8xl mx-auto">

            <div class="mb-8 text-center sm:text-left">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-base-content">Jelajahi Kantin Kampus</h2>
                <p class="text-base-content/60 text-sm mt-1.5 font-medium">Pilih kantin favoritmu untuk melihat
                    menu makanan dan minuman khas mereka.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @forelse ($canteens as $canteen)
                    <x-canteencard :id="$canteen->id" :name="$canteen->name" :image="$canteen->image ? asset('storage/' . $canteen->image) : null" :description="$canteen->description ?? 'Kantin pilihan mahasiswa.'"
                        :menuCount="$canteen->available_menus_count" :actionUrl="route('canteen.show', $canteen->id)" actionText="Lihat Kantin" />
                @empty
                    <div class="col-span-full p-8 text-center bg-vanilla-custard-50 border border-base-200 rounded-3xl">
                        <p class="text-base-content/60 font-medium">Belum ada kantin yang buka saat ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>
@endsection
