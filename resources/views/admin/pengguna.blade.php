@extends('layouts.admin')

@section('title', 'Daftar Pengguna - Admin PNC')

@section('content')

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <!-- Title & Action Buttons Group (Mobile: Title + Icons on one row) -->
        <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
            <h1 class="text-2xl font-bold text-base-content shrink-0">Daftar Pengguna</h1>
            
            <!-- Action Buttons (Mobile only, Icon-only) -->
            <div class="flex md:hidden items-center gap-2">
                <a href="{{ route('admin.pengguna.create') }}"
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
        <form method="GET" action="{{ route('admin.pengguna.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
            <!-- Search Input -->
            <label
                class="input input-bordered flex items-center gap-2 w-full md:w-64 xl:w-80 shadow-sm rounded-full border-base-content/40 focus-within:border-base-content input-md sm:pl-6 grow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="search" name="search" value="{{ request('search') }}" class="grow text-sm sm:text-base font-medium pl-1" placeholder="Cari pengguna..." />
            </label>

            <!-- Filter Button (Below Search on Mobile) -->
            <button type="submit"
                class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 active:scale-95 transition-all w-fit sm:w-auto shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Cari</span>
            </button>

            <!-- Desktop Action Buttons (Icon-only, visible on desktop next to Search/Filter) -->
            <div class="hidden md:flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.pengguna.create') }}"
                    class="btn btn-md bg-fern-700 hover:bg-fern-800 text-white border-none rounded-full w-12 h-12 p-0 shadow-sm active:scale-95 transition-all flex items-center justify-center"
                    title="Tambah Pengguna">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-xl mb-4 text-sm font-bold shadow-sm">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="max-w-5xl">
        <div class="bg-white border border-base-content/15 rounded-2xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto w-full">
                <table class="table w-full min-w-max">
                    <thead>
                        <tr
                            class="bg-base-200 text-xs font-bold uppercase text-base-content/60 border-b border-base-content/10">
                            <th class="py-3 px-4 text-left">Nama</th>
                            <th class="py-3 px-4 text-left">NIM / NIP</th>
                            <th class="py-3 px-4 text-left">Peran</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-content/10">
                        @forelse ($users as $user)
                            <tr class="hover:bg-base-100 transition-colors">
                                <td class="py-3 px-4 font-medium text-sm text-base-content">{{ $user->name }}</td>
                                <td class="py-3 px-4 font-medium text-sm text-base-content/70">{{ $user->nim }}</td>
                                <td class="py-3 px-4 font-medium text-sm text-base-content/70 capitalize">{{ $user->role }}</td>
                                <td class="py-3 px-4 font-medium text-sm">
                                    @if(!$user->is_first_login)
                                        <span class="text-fern-700">Aktif</span>
                                    @else
                                        <span class="text-red-500">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <form action="{{ route('admin.pengguna.toggle', $user->id) }}" method="POST" class="m-0 p-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="btn btn-xs {{ !$user->is_first_login ? 'bg-orange-100 hover:bg-orange-200 text-orange-700' : 'bg-fern-100 hover:bg-fern-200 text-fern-700' }} border-none rounded-md font-bold">
                                                {{ !$user->is_first_login ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.pengguna.edit', $user->id) }}"
                                           class="btn btn-xs bg-amber-500 hover:bg-amber-600 text-white border-none rounded-md font-bold">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" class="m-0 p-0" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-xs bg-red-500 hover:bg-red-600 text-white border-none rounded-md font-bold">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-sm font-medium text-base-content/60">Tidak ada pengguna yang ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

@endsection
