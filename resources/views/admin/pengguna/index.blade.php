@extends('layouts.admin')

@section('title', 'Daftar Pengguna - Admin PNC')

@section('content')

    <div class="max-w-8xl mx-auto pb-10 lg:pb-0" x-data="{
        selectedIds: [],
        allIds: @js($users->pluck('id')->toArray()),
        toggleAll() {
            if (this.selectedIds.length === this.allIds.length) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...this.allIds];
            }
        }
    }" x-cloak>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <!-- Title & Action Buttons Group (Mobile: Title + Icons on one row) -->
            <div class="flex items-center justify-between md:justify-start gap-4 w-full md:w-auto">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Daftar Pengguna</h1>
                        <p class="text-base-content/70 text-sm sm:text-lg font-medium">Kelola akun pengguna sistem.</p>
                    </div>
                    

                </div>
                
                <!-- Action Buttons (Mobile only, Icon-only) -->
                <div class="flex md:hidden items-center gap-2">
                    <a href="{{ route('admin.pengguna.import.form') }}"
                        class="btn bg-base-200 hover:bg-base-300 text-base-content border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm transition-colors flex items-center justify-center"
                        title="Import Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.pengguna.create') }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md p-2.5 h-auto min-h-0 shadow-sm transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Search & Filter Group -->
            <form method="GET" action="{{ route('admin.pengguna.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="w-full md:w-64 xl:w-80 grow relative">
                    <label class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md pr-12">
                        <input type="search" name="search" class="grow text-sm sm:text-base font-medium pl-2" placeholder="Cari pengguna..." value="{{ request('search') }}" />
                    </label>
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </button>
                </div>

                <!-- Filter Button -->
                <button type="submit"
                    class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 transition-colors w-fit sm:w-auto shrink-0">
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
                        class="btn btn-md bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 transition-colors w-fit sm:w-auto shrink-0" title="Hapus filter pencarian">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali</span>
                    </a>
                @endif

                <!-- Desktop Action Buttons -->
                <div class="hidden md:flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.pengguna.import.form') }}"
                        class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content border-none rounded-full w-12 h-12 p-0 shadow-sm transition-colors flex items-center justify-center"
                        title="Import Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.pengguna.create') }}"
                        class="btn btn-md bg-fern-700 hover:bg-fern-800 text-white border-none rounded-full w-12 h-12 p-0 shadow-sm transition-colors flex items-center justify-center"
                        title="Tambah Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- Bulk Actions Panel (Desktop & Mobile) -->
        <div x-show="selectedIds.length > 0" x-transition class="mb-4" style="display: none;" x-cloak>
            
            <!-- Desktop version -->
            <div class="hidden md:flex items-center gap-2 bg-white border border-base-content/15 px-4 py-2 rounded-full shadow-sm w-fit transition-all duration-300">
                <span class="text-sm font-bold text-base-content/70 mr-2"><span x-text="selectedIds.length"></span> pengguna terpilih:</span>
                
                <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 flex items-center" onsubmit="confirmAction(event, 'Yakin ingin mengaktifkan semua pengguna yang terpilih?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="activate">
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id" />
                    </template>
                    <button type="submit" class="btn btn-sm bg-fern-100 hover:bg-fern-200 text-fern-700 border-none rounded-md font-bold px-3">
                        Aktifkan
                    </button>
                </form>

                <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 flex items-center" onsubmit="confirmAction(event, 'Yakin ingin menonaktifkan semua pengguna yang terpilih?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="deactivate">
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id" />
                    </template>
                    <button type="submit" class="btn btn-sm bg-orange-100 hover:bg-orange-200 text-orange-700 border-none rounded-md font-bold px-3">
                        Nonaktifkan
                    </button>
                </form>

                <div class="w-px h-5 bg-base-content/20 mx-2"></div>

                <form action="{{ route('admin.pengguna.bulkDestroy') }}" method="POST" class="m-0 p-0 flex items-center" onsubmit="confirmAction(event, 'Yakin ingin menghapus semua pengguna yang terpilih?', true);">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id" />
                    </template>
                    <button type="submit" class="btn btn-sm bg-red-100 hover:bg-red-200 text-red-700 border-none rounded-md font-bold px-3">
                        Hapus
                    </button>
                </form>
            </div>

            <!-- Mobile version -->
            <div class="flex flex-col md:hidden gap-3 bg-white border border-base-content/15 px-4 py-3 rounded-xl shadow-sm w-full transition-all duration-300">
                <div class="text-xs font-bold text-base-content/70"><span x-text="selectedIds.length"></span> pengguna terpilih</div>
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                    <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 shrink-0" onsubmit="confirmAction(event, 'Yakin ingin mengaktifkan semua pengguna yang terpilih?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="activate">
                        <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id" /></template>
                        <button type="submit" class="btn btn-xs bg-fern-100 hover:bg-fern-200 text-fern-700 border-none rounded-md font-bold px-3">Aktifkan</button>
                    </form>

                    <form action="{{ route('admin.pengguna.bulkToggle') }}" method="POST" class="m-0 p-0 shrink-0" onsubmit="confirmAction(event, 'Yakin ingin menonaktifkan semua pengguna yang terpilih?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="deactivate">
                        <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id" /></template>
                        <button type="submit" class="btn btn-xs bg-orange-100 hover:bg-orange-200 text-orange-700 border-none rounded-md font-bold px-3">Nonaktifkan</button>
                    </form>

                    <form action="{{ route('admin.pengguna.bulkDestroy') }}" method="POST" class="m-0 p-0 shrink-0" onsubmit="confirmAction(event, 'Yakin ingin menghapus semua pengguna yang terpilih?', true);">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]" :value="id" /></template>
                        <button type="submit" class="btn btn-xs bg-red-100 hover:bg-red-200 text-red-700 border-none rounded-md font-bold px-3">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="max-w-8xl">
            <x-admin.pengguna-table :users="$users" />
            
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
    <!-- Global Modals for Actions -->
    <x-modal id="global_confirm_modal" type="warning" title="Konfirmasi">
        <span id="global_confirm_text"></span>
        <x-slot:footer>
            <button type="button" onclick="document.getElementById('global_confirm_modal').close()" class="btn btn-ghost rounded-xl font-bold transition-colors">Batal</button>
            <button type="button" onclick="submitCurrentForm()" class="btn bg-fern-700 hover:bg-fern-800 text-white border-0 rounded-xl font-bold transition-colors">Ya, Lanjutkan</button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="global_delete_modal" type="error" title="Konfirmasi Hapus">
        <span id="global_delete_text"></span>
        <x-slot:footer>
            <button type="button" onclick="document.getElementById('global_delete_modal').close()" class="btn btn-ghost rounded-xl font-bold transition-colors">Batal</button>
            <button type="button" onclick="submitCurrentForm()" class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold transition-colors">Ya, Hapus</button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
<script>
    let currentFormToSubmit = null;
    
    function confirmAction(event, text, isDelete = false) {
        event.preventDefault();
        currentFormToSubmit = event.target;
        
        if (isDelete) {
            document.getElementById('global_delete_text').innerText = text;
            document.getElementById('global_delete_modal').showModal();
        } else {
            document.getElementById('global_confirm_text').innerText = text;
            document.getElementById('global_confirm_modal').showModal();
        }
    }
    
    function submitCurrentForm() {
        if (currentFormToSubmit) {
            currentFormToSubmit.submit();
        }
    }
</script>
@endpush
