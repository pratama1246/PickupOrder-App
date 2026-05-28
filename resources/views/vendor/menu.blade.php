@extends('layouts.vendor')

@section('title', 'Daftar Menu - Vendor PNC')

@section('content')

    <div class="max-w-8xl pb-10 lg:pb-0" id="vendor-menu-container" x-data="initLiveSearch('#vendor-menu-results')">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <!-- Title & Action Button -->
            <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
                <div>
                    <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Daftar Menu</h1>
                    <p class="text-base-content/70 text-sm sm:text-lg font-medium">Kelola semua menu yang ditawarkan kantin Anda.</p>
                </div>
                
                <div class="flex md:hidden items-center gap-2">
                    <a href="{{ route('vendor.menu.create') }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm active:scale-95 transition-all flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Search, Filter & Action Button -->
            <form method="GET" action="{{ route('vendor.menu.index') }}" @submit.prevent class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">

                <div class="w-full md:w-64 xl:w-80 grow relative">
                    <label class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md pr-12">
                        <input type="search" x-model="keyword" class="grow text-sm sm:text-base font-medium pl-2" placeholder="Cari menu..." />
                    </label>
                    <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </button>
                </div>

                @if(request('search'))
                    <!-- Clear Search Button -->
                    <a href="{{ route('vendor.menu.index') }}"
                        class="btn btn-md bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 active:scale-95 transition-all w-fit sm:w-auto shrink-0" title="Kembali ke daftar lengkap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali</span>
                    </a>
                @endif

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



        <div id="vendor-menu-results">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @forelse ($menus as $menu)
                <x-foodcard 
                    :id="$menu->id"
                    :name="$menu->name"
                    :canteenName="$canteen->name"
                    :description="$menu->description"
                    :price="$menu->formatted_price"
                    :image="$menu->image ? asset('storage/' . $menu->image) : null"
                    :rating="number_format($menu->average_rating, 1)"
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
                            <button type="button" onclick="openDeleteModal('{{ addslashes($menu->name) }}', '{{ route('vendor.menu.destroy', $menu->id) }}')" class="text-red-400 hover:text-red-600 transition-colors pt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
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
    </div>

    <!-- Global Delete Modal -->
    <x-modal id="global_delete_modal" type="error" title="Hapus Menu" subtitle="Tindakan ini tidak dapat dibatalkan">
        Apakah Anda yakin ingin menghapus menu <strong id="delete_menu_name"></strong>? Data pesanan yang terkait mungkin akan ikut terarsipkan.

        <x-slot:footer>
            <button type="button" onclick="document.getElementById('global_delete_modal').close()" class="btn btn-ghost rounded-xl font-bold active:scale-95 transition-all">
                Batal
            </button>
            <form id="delete_menu_form" method="POST" class="inline-block m-0 p-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white border-0 shadow-md rounded-xl font-bold active:scale-95 transition-all">
                    Ya, Hapus
                </button>
            </form>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
<script>
    function openDeleteModal(name, url) {
        document.getElementById('delete_menu_name').innerText = name;
        document.getElementById('delete_menu_form').action = url;
        document.getElementById('global_delete_modal').showModal();
    }
</script>
@endpush
