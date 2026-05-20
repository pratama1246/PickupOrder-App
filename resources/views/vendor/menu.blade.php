@extends('layouts.vendor')

@section('title', 'Daftar Menu - Vendor PNC')

@section('content')

    <div class="max-w-8xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <!-- Title & Action Buttons Group (Mobile: Title + Icons on one row) -->
            <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
                <h1 class="text-2xl font-bold text-base-content shrink-0">Daftar Menu</h1>
                
                <!-- Action Buttons (Mobile only, Icon-only) -->
                <div class="flex md:hidden items-center gap-2">
                    <a href="{{ route('vendor.menu.create') }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm active:scale-95 transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Search, Filter & Action Buttons Group (Desktop: side-by-side on right) -->
            <form method="GET" action="{{ route('vendor.menu.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <label
                    class="input input-bordered flex items-center gap-2 w-full md:w-64 xl:w-80 shadow-sm rounded-full border-base-content/40 focus-within:border-base-content input-md sm:pl-6 grow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="search" name="search" value="{{ request('search') }}" class="grow text-sm sm:text-base font-medium pl-1" placeholder="Cari menu..." />
                </label>

                <!-- Filter Button (Below Search on Mobile) -->
                <button type="submit"
                    class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 active:scale-95 transition-all w-fit sm:w-auto shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-base-content/70" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Cari</span>
                </button>

                <!-- Action Buttons (Desktop only, side-by-side with labels) -->
                <div class="hidden md:flex items-center gap-2 shrink-0">
                    <a href="{{ route('vendor.menu.create') }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white font-bold text-sm border-none rounded-md px-6 py-2.5 h-auto min-h-0 shadow-sm active:scale-95 transition-all flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah Menu</span>
                    </a>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-xl mb-4 text-sm font-bold shadow-sm">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @forelse ($menus as $menu)
                <x-foodcard 
                    :id="$menu->id"
                    :name="$menu->name"
                    :canteenName="$canteen->name"
                    :description="$menu->description"
                    :price="$menu->formatted_price"
                    :image="$menu->image ? asset('storage/' . $menu->image) : null"
                    rating="4.8"
                    actionUrl="#"
                >
                    <x-slot:action>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('vendor.menu.edit', $menu->id) }}" class="text-base-content/60 hover:text-fern-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form action="{{ route('vendor.menu.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Hapus menu ini?')" class="inline-block m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition-colors pt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </x-slot:action>
                </x-foodcard>
            @empty
                <div class="col-span-full p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl mt-4">
                    <p class="text-base-content/60 font-medium">Belum ada menu yang didaftarkan.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-6">
            {{ $menus->links() }}
        </div>
    </div>

@endsection
