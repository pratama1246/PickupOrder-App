@extends('layouts.admin')

@section('title', 'Daftar Kantin - Admin PNC')

@section('content')

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <!-- Title & Action Buttons Group (Mobile: Title + Icons on one row) -->
        <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
            <h1 class="text-2xl font-bold text-base-content shrink-0">Daftar Kantin</h1>
            
            <!-- Action Buttons (Mobile only, Icon-only) -->
            <div class="flex md:hidden items-center gap-2">
                <a href="/admin/kantin/tambah"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm active:scale-95 transition-all flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
                <button
                    class="btn bg-red-500 hover:bg-red-600 text-white border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm active:scale-95 transition-all flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Search & Filter Group -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
            <!-- Search Input -->
            <label
                class="input input-bordered flex items-center gap-2 w-full md:w-64 xl:w-80 shadow-sm rounded-full border-base-content/40 focus-within:border-base-content input-md sm:pl-6 grow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="search" class="grow text-sm sm:text-base font-medium pl-1" placeholder="Cari kantin..." />
            </label>

            <!-- Filter Button (Below Search on Mobile) -->
            <button
                class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 active:scale-95 transition-all w-fit sm:w-auto shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Filter</span>
            </button>

            <!-- Desktop Action Buttons (Icon-only, visible on desktop next to Search/Filter) -->
            <div class="hidden md:flex items-center gap-2 shrink-0">
                <a href="/admin/kantin/tambah"
                    class="btn btn-md bg-fern-700 hover:bg-fern-800 text-white border-none rounded-full w-12 h-12 p-0 shadow-sm active:scale-95 transition-all flex items-center justify-center"
                    title="Tambah Kantin">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
                <button
                    class="btn btn-md bg-red-500 hover:bg-red-600 text-white border-none rounded-full w-12 h-12 p-0 shadow-sm active:scale-95 transition-all flex items-center justify-center"
                    title="Hapus Kantin">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="space-y-4 max-w-4xl">
        @foreach ([1, 2] as $i)
            <x-canteencard id="{{ $i }}" name="Kantin {{ $i }}"
                image="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80"
                description="Menyediakan berbagai pilihan menu lezat dengan harga terjangkau untuk mahasiswa dan civitas akademik."
                actionText="Detail" actionUrl="/admin/kantin/{{ $i }}" />
        @endforeach
    </div>

@endsection
