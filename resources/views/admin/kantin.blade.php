@extends('layouts.admin')

@section('title', 'Daftar Kantin - Admin PNC')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <h1 class="text-2xl font-bold text-base-content">Daftar Kantin</h1>

    <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm font-bold text-base-content/60 hidden sm:inline">Filter By:</span>
        <label class="input input-bordered input-sm flex items-center gap-2 rounded-full border-base-content/30 w-48">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="search" placeholder="Cari kantin..." class="grow text-sm" />
        </label>

        <a href="/admin/kantin/tambah"
           class="btn btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md font-bold text-xs shadow-sm">
            Tambah Kantin
        </a>
        <button class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-none rounded-md font-bold text-xs shadow-sm">
            Hapus Kantin
        </button>
    </div>
</div>

<div class="space-y-4 max-w-4xl">

    @foreach([1, 2] as $i)
        <x-canteencard 
            id="{{ $i }}"
            name="Kantin {{ $i }}"
            image="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80"
            description="Menyediakan berbagai pilihan menu lezat dengan harga terjangkau untuk mahasiswa dan civitas akademik."
            actionText="Detail"
            actionUrl="/admin/kantin/{{ $i }}"
        />
    @endforeach

</div>

@endsection
