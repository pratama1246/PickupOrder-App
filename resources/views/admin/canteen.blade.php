@extends('layouts.admin')

@section('title', 'Daftar Kantin - Admin PNC')

@section('content')

    {{-- 
      Menggunakan fungsi global `initLiveSearch` untuk mencari data kantin secara real-time via AJAX 
      sehingga admin tidak perlu memuat ulang halaman ketika mengetik kata kunci.
    --}}
    <div id="canteens-container" x-data="initLiveSearch('#canteens-results')">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-2">Daftar Kantin</h1>
                <p class="text-base-content/70 text-sm sm:text-lg font-medium">Kelola seluruh data kantin yang terdaftar.</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-start gap-3 w-full lg:w-auto">
                <form action="" method="GET" @submit.prevent class="w-full md:w-64 xl:w-80 grow relative">
                    <label
                        class="input input-bordered flex items-center w-full shadow-sm rounded-3xl border-base-content/40 focus-within:border-base-content input-md pr-12">
                        <input type="text" x-model="keyword" class="grow text-sm sm:text-base font-medium pl-2"
                            placeholder="Cari kantin..." />
                    </label>
                    <button type="button" @click="if(keyword.trim()) triggerSearch()"
                        class="absolute right-2 top-1/2 -translate-y-1/2 btn btn-circle btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none min-h-0 w-8 h-8 transition-all duration-200 active:scale-95 flex items-center justify-center cursor-pointer"
                        title="Cari">
                        <svg x-show="!loading" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.35-4.35" />
                        </svg>
                        <span x-show="loading" class="loading loading-spinner loading-xs text-white" style="display: none;"></span>
                    </button>
                </form>

                <div class="flex lg:hidden items-center w-full sm:w-auto">
                    <a href="{{ route('admin.canteen.create') }}"
                        class="btn bg-fern-700 hover:bg-fern-800 text-white border-none rounded-xl py-3 h-auto shadow-sm transition-all duration-200 active:scale-95 flex items-center justify-center gap-2 font-bold w-full sm:w-auto sm:px-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Tambah Kantin</span>
                    </a>
                </div>


                <div class="hidden lg:flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.canteen.create') }}"
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

        <div id="canteens-results">
            <div class="space-y-4 max-w-4xl pb-10 lg:pb-0">
                @forelse ($canteens as $canteen)
                    <x-canteencard :id="$canteen->id" :name="$canteen->name" :image="$canteen->image ? asset('storage/' . $canteen->image) : null" :description="$canteen->description" :menuCount="$canteen->menus_count"
                        :rating="number_format($canteen->average_rating, 1)" actionText="Detail" :actionUrl="route('admin.canteen.show', $canteen->id)" />
                @empty
                    <div class="p-8 text-center bg-vanilla-custard-50 border border-base-content/25 rounded-3xl">
                        <p class="text-base-content/60 font-medium">Belum ada kantin terdaftar.</p>
                    </div>
                @endforelse

                <div class="pt-4">
                    {{ $canteens->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
