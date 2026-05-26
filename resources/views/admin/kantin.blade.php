@extends('layouts.admin')

@section('title', 'Daftar Kantin - Admin PNC')

@section('content')

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <!-- Title & Action Buttons Group (Mobile: Title + Icons on one row) -->
        <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Daftar Kantin</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Kelola seluruh data kantin yang terdaftar.</p>
            </div>
 
            <!-- Action Buttons (Mobile only, Icon-only) -->
            <div class="flex md:hidden items-center gap-2">
                <a href="{{ route('admin.kantin.create') }}"
                    class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm transition-colors flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
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
                class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 transition-colors w-fit sm:w-auto shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-base-content/70" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Filter</span>
            </button>
 
            <!-- Desktop Action Buttons (Icon-only, visible on desktop next to Search/Filter) -->
            <div class="hidden md:flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.kantin.create') }}"
                    class="btn btn-md bg-fern-700 hover:bg-fern-800 text-white border-none rounded-full w-12 h-12 p-0 shadow-sm transition-colors flex items-center justify-center"
                    title="Tambah Kantin">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
 
    <div class="space-y-4 max-w-4xl">
        @forelse ($canteens as $canteen)
            <div class="bg-white rounded-xl shadow-sm border border-base-200">
                <x-canteencard :id="$canteen->id" :name="$canteen->name" :image="$canteen->image ? asset('storage/' . $canteen->image) : null" :description="$canteen->description" :menuCount="$canteen->menus_count"
                    rating="4.8" actionText="Detail" :actionUrl="route('admin.kantin.show', $canteen->id)" />
            </div>
        @empty
            <div class="p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl">
                <p class="text-base-content/60 font-medium">Belum ada kantin terdaftar.</p>
            </div>
        @endforelse
 
        <div class="pt-4">
            {{ $canteens->links() }}
        </div>
    </div>


@endsection
