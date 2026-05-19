@extends('layouts.app')

@section('title', 'Kantin 1 - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-12">
    
    <x-breadcrumb :links="[
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Kantin', 'url' => '/pesan'],
        ['label' => 'Kantin 1']
    ]" />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-8">
        <div class="max-w-8xl mx-auto">
            <div class="relative w-full aspect-3/4 sm:aspect-video rounded-3xl overflow-hidden shadow-lg border border-base-content/10 bg-base-200">
                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" 
                     onerror="this.src='https://ui-avatars.com/api/?name=Kantin+1&background=random'"
                     alt="Kantin 1" 
                     class="absolute inset-0 w-full h-full object-cover">
                
                <div class="absolute inset-0 bg-linear-to-t from-black/95 via-black/40 to-transparent flex flex-col justify-end p-6 sm:p-10 md:p-14">
                    <div class="max-w-4xl text-white">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-3 sm:mb-4">
                            <div class="badge bg-fern-700 text-white border-none px-3 sm:px-4 py-2 sm:py-3 font-bold text-[10px] sm:text-xs shadow-md">BUKA</div>
                            <div class="flex items-center gap-1.5 bg-white/20 backdrop-blur-md px-2 sm:px-3 py-1 rounded-lg text-white font-bold border border-white/30 text-xs sm:text-sm shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                4.7
                            </div>
                        </div>
                        
                        <h1 class="text-3xl sm:text-5xl md:text-6xl font-bold text-white mb-2 sm:mb-4 drop-shadow-2xl tracking-tight">Kantin 1</h1>
                        
                        <p class="text-white/80 text-sm sm:text-lg max-w-2xl mb-4 sm:mb-8 line-clamp-2 sm:line-clamp-none font-medium drop-shadow-sm">
                            Kantin yang menyediakan nasi goreng, mi bakso, dan berbagai macam pilihan menu lezat lainnya dengan harga terjangkau khusus mahasiswa.
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-4 sm:gap-8 text-white/90 text-xs sm:text-sm font-bold drop-shadow-sm">
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-fern-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                25 Menu
                            </span>
                            <span class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 text-fern-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                07.00 - 16.00
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-8">
        <div class="max-w-8xl mx-auto flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
            
            <div class="flex items-center gap-3">
                <span class="text-sm sm:text-base font-bold text-base-content/70 hidden sm:inline">Filter By:</span>
                <select class="select select-bordered select-md rounded-full border-base-content/40 w-full sm:w-auto min-w-56 focus:outline-none font-bold text-sm sm:text-base">
                    <option disabled selected>Semua Makanan & Minuman</option>
                    <option>Makanan</option>
                    <option>Minuman</option>
                </select>
            </div>
            
            <label class="input input-bordered flex items-center gap-2 w-full sm:max-w-md shadow-sm rounded-full border-base-content/40 focus-within:border-base-content input-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="search" class="grow text-sm sm:text-base font-medium" placeholder="Cari menu favoritmu..." />
            </label>

        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 mb-12">
        <div class="max-w-8xl mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <x-foodcard />
                <x-foodcard />
                <x-foodcard />
                <x-foodcard />
                <x-foodcard />
                <x-foodcard />
            </div>
        </div>
    </section>

</main>
@endsection
