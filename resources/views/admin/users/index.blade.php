@extends('layouts.admin')

@section('title', 'Daftar Pengguna - Admin PNC')

@section('content')

    {{-- 
      Container utama daftar pengguna.
      Menggunakan live search berbasis AJAX untuk pencarian real-time tanpa reload 
      serta menyimpan array `selectedIds` untuk memproses aksi massal (bulk actions).
    --}}
    <div class="max-w-8xl mx-auto pb-10 lg:pb-0" id="users-container" x-data="{
        ...initLiveSearch('#users-results'),
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
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Daftar Pengguna</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Kelola akun pengguna sistem.</p>
            </div>
            <form method="GET" action="{{ route('admin.users.index') }}" @submit.prevent
                class="flex flex-col sm:flex-row items-stretch sm:items-center justify-start gap-3 w-full lg:w-auto">
                <div class="w-full sm:w-64 xl:w-80 grow relative">
                    <label
                        class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md pr-12">
                        <input type="text" x-model="keyword" class="grow text-sm sm:text-base font-medium pl-2"
                            placeholder="Cari pengguna..." />
                    </label>
                    <button type="button"
                        class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center cursor-pointer"
                        title="Cari">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                    </button>
                </div>


                <button type="button" x-show="keyword" x-cloak @click="keyword = ''" x-transition
                    class="btn btn-md bg-rose-50 hover:bg-rose-100 text-rose-600 text-sm font-bold border-none rounded-full px-5 flex items-center justify-center gap-2 transition-colors w-fit sm:w-auto shrink-0"
                    title="Hapus pencarian">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Hapus</span>
                </button>

                <div class="flex lg:hidden items-center gap-2.5 w-full sm:w-auto">
                    <a href="{{ route('admin.users.import.form') }}"
                        class="btn bg-base-200 hover:bg-base-300 text-base-content border-none rounded-xl py-3 h-auto shadow-sm transition-all duration-200 active:scale-95 flex-1 sm:flex-initial sm:px-6 flex items-center justify-center gap-2 font-bold"
                        title="Impor Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/70" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Import CSV</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl py-3 h-auto shadow-sm transition-all duration-200 active:scale-95 flex-1 sm:flex-initial sm:px-6 flex items-center justify-center gap-2 font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah</span>
                    </a>
                </div>

                <div class="hidden lg:flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.users.import.form') }}"
                        class="btn btn-md bg-base-200 hover:bg-base-300 text-base-content border-none rounded-full w-12 h-12 p-0 shadow-sm transition-colors flex items-center justify-center"
                        title="Import Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/70" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.users.create') }}"
                        class="btn btn-md bg-fern-700 hover:bg-fern-800 text-white border-none rounded-full w-12 h-12 p-0 shadow-sm transition-colors flex items-center justify-center"
                        title="Tambah Pengguna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        {{-- 
          Panel Bulk Actions: Hanya muncul saat ada satu atau lebih baris yang dicentang.
          Menyediakan aksi massal untuk aktifkan/nonaktifkan/hapus akun terpilih sekaligus.
        --}}
        <div x-show="selectedIds.length > 0" x-transition class="mb-4" style="display: none;" x-cloak>

            <div
                class="hidden md:flex items-center gap-2 bg-white border border-base-content/15 px-4 py-2 rounded-full shadow-sm w-fit transition-all duration-300">
                <span class="text-sm font-bold text-base-content/70 mr-2"><span x-text="selectedIds.length"></span> pengguna
                    terpilih:</span>

                <form action="{{ route('admin.users.bulkToggle') }}" method="POST" class="m-0 p-0 flex items-center"
                    onsubmit="confirmAction(event, 'Yakin ingin mengaktifkan semua pengguna yang terpilih?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="activate">
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id" />
                    </template>
                    <button type="submit"
                        class="btn btn-sm bg-fern-100 hover:bg-fern-200 text-fern-700 border-none rounded-md font-bold px-3">
                        Aktifkan
                    </button>
                </form>

                <form action="{{ route('admin.users.bulkToggle') }}" method="POST" class="m-0 p-0 flex items-center"
                    onsubmit="confirmAction(event, 'Yakin ingin menonaktifkan semua pengguna yang terpilih?');">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="action" value="deactivate">
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id" />
                    </template>
                    <button type="submit"
                        class="btn btn-sm bg-orange-100 hover:bg-orange-200 text-orange-700 border-none rounded-md font-bold px-3">
                        Nonaktifkan
                    </button>
                </form>

                <div class="w-px h-5 bg-base-content/20 mx-2"></div>

                <form action="{{ route('admin.users.bulkDestroy') }}" method="POST" class="m-0 p-0 flex items-center"
                    onsubmit="confirmAction(event, 'Yakin ingin menghapus semua pengguna yang terpilih?', true);">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="ids[]" :value="id" />
                    </template>
                    <button type="submit"
                        class="btn btn-sm bg-red-100 hover:bg-red-200 text-red-700 border-none rounded-md font-bold px-3">
                        Hapus
                    </button>
                </form>
            </div>

            <div
                class="flex flex-col md:hidden gap-3 bg-white border border-base-content/15 px-4 py-3 rounded-xl shadow-sm w-full transition-all duration-300">
                <div class="text-xs font-bold text-base-content/70"><span x-text="selectedIds.length"></span> pengguna
                    terpilih</div>
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-hide">
                    <form action="{{ route('admin.users.bulkToggle') }}" method="POST" class="m-0 p-0 shrink-0"
                        onsubmit="confirmAction(event, 'Yakin ingin mengaktifkan semua pengguna yang terpilih?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="activate">
                        <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]"
                                :value="id" /></template>
                        <button type="submit"
                            class="btn btn-xs bg-fern-100 hover:bg-fern-200 text-fern-700 border-none rounded-md font-bold px-3">Aktifkan</button>
                    </form>

                    <form action="{{ route('admin.users.bulkToggle') }}" method="POST" class="m-0 p-0 shrink-0"
                        onsubmit="confirmAction(event, 'Yakin ingin menonaktifkan semua pengguna yang terpilih?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="action" value="deactivate">
                        <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]"
                                :value="id" /></template>
                        <button type="submit"
                            class="btn btn-xs bg-orange-100 hover:bg-orange-200 text-orange-700 border-none rounded-md font-bold px-3">Nonaktifkan</button>
                    </form>

                    <form action="{{ route('admin.users.bulkDestroy') }}" method="POST" class="m-0 p-0 shrink-0"
                        onsubmit="confirmAction(event, 'Yakin ingin menghapus semua pengguna yang terpilih?', true);">
                        @csrf
                        @method('DELETE')
                        <template x-for="id in selectedIds" :key="id"><input type="hidden" name="ids[]"
                                :value="id" /></template>
                        <button type="submit"
                            class="btn btn-xs bg-red-100 hover:bg-red-200 text-red-700 border-none rounded-md font-bold px-3">Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        <div id="users-results" class="max-w-8xl">
            <x-admin.users-table :users="$users" />

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
    {{-- 
      Modals Konfirmasi Global:
      Menggunakan satu instance modal bersama demi menghemat ukuran DOM (tidak menduplikasi modal di tiap baris).
      Form aktif yang memicu konfirmasi disimpan di variable global JavaScript `currentFormToSubmit`.
    --}}
    <x-modal id="global_confirm_modal" type="warning" title="Konfirmasi">
        <span id="global_confirm_text"></span>
        <x-slot:footer>
            <button type="button" onclick="document.getElementById('global_confirm_modal').close()"
                class="btn btn-ghost rounded-xl font-bold transition-colors">Batal</button>
            <button type="button" onclick="submitCurrentForm()"
                class="btn bg-fern-700 hover:bg-fern-800 text-white border-0 rounded-xl font-bold transition-colors">Ya,
                Lanjutkan</button>
        </x-slot:footer>
    </x-modal>

    <x-modal id="global_delete_modal" type="error" title="Konfirmasi Hapus">
        <span id="global_delete_text"></span>
        <x-slot:footer>
            <button type="button" onclick="document.getElementById('global_delete_modal').close()"
                class="btn btn-ghost rounded-xl font-bold transition-colors">Batal</button>
            <button type="button" onclick="submitCurrentForm()"
                class="btn bg-red-600 hover:bg-red-700 text-white border-0 rounded-xl font-bold transition-colors">Ya,
                Hapus</button>
        </x-slot:footer>
    </x-modal>
@endsection

@push('scripts')
    <script>
        // Menyimpan referensi form aktif agar modal konfirmasi global tahu form mana yang harus di-submit saat tombol konfirmasi diklik
        let currentFormToSubmit = null;

        // Mencegah pengiriman form instan untuk memicu modal konfirmasi DaisyUI terlebih dahulu demi UX yang konsisten
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

        // Dipanggil saat pengguna menekan tombol "Ya/Setuju" di dalam modal konfirmasi global
        function submitCurrentForm() {
            if (currentFormToSubmit) {
                currentFormToSubmit.submit();
            }
        }
    </script>
@endpush
