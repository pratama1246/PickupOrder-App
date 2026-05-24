@extends('layouts.admin')

@section('title', 'Detail Kantin - Admin PNC')

@section('content')

<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.kantin.index') }}" class="btn btn-sm btn-ghost gap-1 px-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Daftar Kantin
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-base-200">
        <x-canteencard 
            :id="$canteen->id" 
            :name="$canteen->name"
            :image="$canteen->image ? asset('storage/' . $canteen->image) : null"
            :description="$canteen->description"
            :menuCount="$canteen->menus_count"
            rating="4.8"
        >
            <x-slot:buttons>
                <form action="{{ route('admin.kantin.destroy', $canteen->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kantin ini?')" class="inline-block m-0 p-0 w-full md:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn bg-red-500 text-white hover:bg-red-600 shadow-lg rounded-xl w-full md:w-fit">
                        Hapus
                    </button>
                </form>
                <a href="{{ route('admin.kantin.edit', $canteen->id) }}" class="btn bg-fern-700 text-white hover:bg-fern-800 shadow-lg rounded-xl w-full md:w-fit text-center">
                    Edit
                </a>
            </x-slot:buttons>
        </x-canteencard>
    </div>
</div>

@endsection
