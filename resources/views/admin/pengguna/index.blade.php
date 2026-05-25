@extends('layouts.admin')

@section('title', 'Daftar Pengguna - Admin PNC')

@section('content')

    <div x-data="{
        selectedIds: [],
        allIds: @js($users->pluck('id')->toArray()),
        toggleAll() {
            if (this.selectedIds.length === this.allIds.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...this.allIds];
            }
        }
    }">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <!-- Title & Action Buttons Group (Mobile: Title + Icons on one row) -->
            <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-base-content shrink-0">Daftar Pengguna</h1>
                    
                    <!-- Desktop Bulk Actions -->
                    <div x-show="selectedIds.length > 0" x-transition class="hidden md:flex items-center gap-2 bg-white border border-base-content/15 px-3 py-1.5 rounded-full shadow-sm shrink-0" style="display: none;">
                        <span class="text-xs font-bold text-base-content/70 mr-1"><span x-text="selectedIds.length"></span> terpilih:</span>
                        
                        <!-- Aktifkan -->
                        <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 flex items-center" onsubmit="return confirm('Yakin ingin mengaktifkan semua pengguna yang terpilih?');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="activate">
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id" />
                            </template>
                            <button type="submit" class="btn btn-xs bg-fern-100 hover:bg-fern-200 text-fern-700 border-none rounded-md font-bold px-2 py-0.5">
                                Aktifkan
                            </button>
                        </form>

                        <!-- Nonaktifkan -->
                        <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 flex items-center" onsubmit="return confirm('Yakin ingin menonaktifkan semua pengguna yang terpilih?');">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="deactivate">
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id" />
                            </template>
                            <button type="submit" class="btn btn-xs bg-orange-100 hover:bg-orange-200 text-orange-700 border-none rounded-md font-bold px-2 py-0.5">
                                Nonaktifkan
                            </button>
                        </form>

                        <div class="w-px h-4 bg-base-content/20 mx-1"></div>

                        <!-- Hapus -->
                        <form action="{{ route('admin.pengguna.bulkDestroy') }}" method="POST" class="m-0 p-0 flex items-center" onsubmit="return confirm('Yakin ingin menghapus semua pengguna yang terpilih?');">
                            @csrf
                            @method('DELETE')
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id" />
                            </template>
                            <button type="submit" class="btn btn-xs bg-red-100 hover:bg-red-200 text-red-700 border-none rounded-md font-bold px-2 py-0.5">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Action Buttons (Mobile only, Icon-only) -->
                <div class="flex md:hidden items-center gap-2">
                    <a href="{{ route('admin.pengguna.import.form') }}"
                        class="btn bg-base-200 hover:bg-base-300 text-base-content border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm active:scale-95 transition-all flex items-center justify-center"
                        title="Import Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.pengguna.create') }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm active:scale-95 transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
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

                <!-- Filter Button -->
                <button type="submit"
                    class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 active:scale-95 transition-all w-fit sm:w-auto shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filter</span>
                </button>

                @if(request('search') || request('role'))
                    <!-- Clear Search Button -->
                    <a href="{{ route('admin.pengguna.index') }}"
                        class="btn btn-md bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 active:scale-95 transition-all w-fit sm:w-auto shrink-0" title="Hapus filter pencarian">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali</span>
                    </a>
                @endif

                <!-- Desktop Action Buttons -->
                <div class="hidden md:flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.pengguna.import.form') }}"
                        class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content border-none rounded-full w-12 h-12 p-0 shadow-sm active:scale-95 transition-all flex items-center justify-center"
                        title="Import Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </a>
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

        <!-- Mobile Bulk Actions Panel -->
        <div x-show="selectedIds.length > 0" x-transition class="flex flex-col md:hidden gap-3 bg-white border border-base-content/15 px-4 py-3 rounded-xl shadow-sm w-full mb-4" style="display: none;">
            <div class="text-xs font-bold text-base-content/70"><span x-text="selectedIds.length"></span> pengguna terpilih</div>
            <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 shrink-0" onsubmit="return confirm('Yakin ingin mengaktifkan semua pengguna yang terpilih?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="activate">
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id" /></template>
                    <button type="submit" class="btn btn-xs bg-fern-100 hover:bg-fern-200 text-fern-700 border-none rounded-md font-bold px-3">Aktifkan</button>
                </form>

                <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 shrink-0" onsubmit="return confirm('Yakin ingin menonaktifkan semua pengguna yang terpilih?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="deactivate">
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id" /></template>
                    <button type="submit" class="btn btn-xs bg-orange-100 hover:bg-orange-200 text-orange-700 border-none rounded-md font-bold px-3">Nonaktifkan</button>
                </form>

                <form action="{{ route('admin.pengguna.bulkDestroy') }}" method="POST" class="m-0 p-0 shrink-0" onsubmit="return confirm('Yakin ingin menghapus semua pengguna yang terpilih?');">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id" /></template>
                    <button type="submit" class="btn btn-xs bg-red-100 hover:bg-red-200 text-red-700 border-none rounded-md font-bold px-3">Hapus</button>
                </form>
            </div>
        </div>

        @if(session('error_list'))
            <div class="alert alert-error rounded-xl mb-4 text-sm shadow-sm flex flex-col items-start gap-2">
                <span class="font-bold text-red-800">Beberapa baris data gagal diimpor:</span>
                <ul class="list-disc pl-5 text-xs text-red-700 space-y-1 font-medium">
                    @foreach(session('error_list') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="max-w-5xl">
            <div class="bg-white border border-base-content/15 rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto w-full">
                    <table class="table w-full min-w-max">
                        <thead>
                            <tr
                                class="bg-base-200 text-xs font-bold uppercase text-base-content/60 border-b border-base-content/10">
                                <th class="py-3 px-4 text-left w-12">
                                    <input type="checkbox" 
                                        class="checkbox checkbox-sm border-base-content/30 focus:ring-0 checked:bg-fern-700 checked:text-white"
                                        :checked="selectedIds.length === allIds.length && allIds.length > 0"
                                        @change="toggleAll()" />
                                </th>
                                <th class="py-3 px-4 text-left">Nama</th>
                                <th class="py-3 px-4 text-left">Email</th>
                                <th class="py-3 px-4 text-left">NIM / NIP</th>
                                <th class="py-3 px-4 text-left">Peran</th>
                                <th class="py-3 px-4 text-left">Status</th>
                                <th class="py-3 px-4 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-base-content/10">
                            @forelse ($users as $user)
                                <tr class="hover:bg-base-100 transition-colors" :class="selectedIds.includes({{ $user->id }}) ? 'bg-fern-50/40' : ''">
                                    <td class="py-3 px-4 w-12">
                                        <input type="checkbox" 
                                            value="{{ $user->id }}"
                                            class="checkbox checkbox-sm border-base-content/30 focus:ring-0 checked:bg-fern-700 checked:text-white"
                                            x-model.number="selectedIds" />
                                    </td>
                                    <td class="py-3 px-4 font-medium text-sm text-base-content">{{ $user->name }}</td>
                                    <td class="py-3 px-4 font-medium text-sm text-base-content/70">{{ $user->email ?? '-' }}</td>
                                    <td class="py-3 px-4 font-medium text-sm text-base-content/70">{{ $user->nim ?? '-' }}</td>
                                    <td class="py-3 px-4 font-medium text-sm text-base-content/70 capitalize">{{ $user->role }}</td>
                                    <td class="py-3 px-4 font-medium text-sm">
                                        @if(!$user->is_first_login)
                                            <span class="text-fern-700 font-semibold">Aktif</span>
                                        @else
                                            <span class="text-red-500 font-semibold">Nonaktif</span>
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
                                    <td colspan="7" class="py-8 text-center text-sm font-medium text-base-content/60">Tidak ada pengguna yang ditemukan.</td>
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
    </div>

@endsection
