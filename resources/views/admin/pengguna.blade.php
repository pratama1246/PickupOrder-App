@extends('layouts.admin')

@section('title', 'Daftar Pengguna - Admin PNC')

@section('content')

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <h1 class="text-2xl font-bold text-base-content">Daftar Pengguna</h1>

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm font-bold text-base-content/60 hidden sm:inline">Filter By:</span>
            <label class="input input-bordered input-sm flex items-center gap-2 rounded-full border-base-content/30 w-48">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-base-content/40" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8" />
                    <path d="m21 21-4.35-4.35" />
                </svg>
                <input type="search" placeholder="Cari pengguna..." class="grow text-sm" />
            </label>

            <a href="/admin/pengguna/tambah"
                class="btn btn-sm bg-fern-700 hover:bg-fern-800 text-white border-none rounded-md font-bold text-xs shadow-sm">
                Tambah Pengguna
            </a>
            <button
                class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-none rounded-md font-bold text-xs shadow-sm">
                Hapus Pengguna
            </button>
        </div>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white border border-base-content/15 rounded-2xl overflow-hidden shadow-sm">
            <table class="table w-full">
                <thead>
                    <tr
                        class="bg-base-200 text-xs font-bold uppercase text-base-content/60 border-b border-base-content/10">
                        <th class="py-3 px-4 text-left">Nama</th>
                        <th class="py-3 px-4 text-left">Nomor ID</th>
                        <th class="py-3 px-4 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-content/10">

                    @foreach ([['name' => 'KNT00127', 'nim' => '240200127', 'label' => 'Kantin 1'], ['name' => '240200115', 'nim' => '240200115', 'label' => 'Pratama Putra'], ['name' => '23688775898749552', 'nim' => '23688775898749552', 'label' => 'Jung Sungchan'], ['name' => '44558844192555558', 'nim' => '44558844192555558', 'label' => 'Lee Sahee']] as $user)
                        <tr class="hover:bg-base-100 transition-colors">
                            <td class="py-3 px-4 font-medium text-sm text-base-content">{{ $user['label'] }}</td>
                            <td class="py-3 px-4 font-medium text-sm text-base-content/70">{{ $user['nim'] }}</td>
                            <td class="py-3 px-4">
                                <div class="flex gap-2">
                                    <button
                                        class="btn btn-xs bg-base-200 hover:bg-base-300 text-base-content border-none rounded-md font-bold">
                                        Detail
                                    </button>
                                    <button
                                        class="btn btn-xs bg-red-500 hover:bg-red-600 text-white border-none rounded-md font-bold">
                                        Nonaktif
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

@endsection
