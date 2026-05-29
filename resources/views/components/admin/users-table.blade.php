@props(['users'])

{{-- 
  Komponen Tabel Pengguna Admin:
  - Menyajikan antarmuka tabular untuk manajemen akun pengguna (Mahasiswa, Vendor, Admin).
  - Berinteraksi dengan state Alpine.js di halaman induk ('selectedIds' dan 'allIds') 
    untuk mengelola pilihan massal (bulk selection) secara langsung di sisi klien, 
    termasuk perubahan warna latar baris tabel yang dicentang.
  - Memetakan properti model '!is_first_login' menjadi label status (Aktif/Nonaktif).
  - Memuat form aksi inline untuk mengubah status keaktifan pengguna (Toggle Status), tombol navigasi edit, 
    dan tombol penghapusan dengan validasi konfirmasi global ('confirmAction').
--}}

<div class="bg-white border border-base-content/15 rounded-2xl overflow-hidden shadow-sm">
    <div class="overflow-x-auto w-full">
        <table class="table w-full min-w-max">
            <thead>
                <tr class="bg-base-200 text-xs font-bold uppercase text-base-content/60 border-b border-base-content/10">
                    <th class="py-3 px-4 text-left w-12">
                        <input type="checkbox"
                            class="checkbox checkbox-sm border-base-content/30 focus:ring-0 checked:bg-fern-700 checked:text-white"
                            :checked="selectedIds.length === allIds.length && allIds.length > 0" @change="toggleAll()" />
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
                    <tr class="hover:bg-base-100 transition-colors"
                        :class="selectedIds.includes({{ $user->id }}) ? 'bg-fern-50/40' : ''">
                        <td class="py-3 px-4 w-12">
                            <input type="checkbox" value="{{ $user->id }}"
                                class="checkbox checkbox-sm border-base-content/30 focus:ring-0 checked:bg-fern-700 checked:text-white"
                                x-model.number="selectedIds" />
                        </td>
                        <td class="py-3 px-4 font-medium text-sm text-base-content">{{ $user->name }}</td>
                        <td class="py-3 px-4 font-medium text-sm text-base-content/70">{{ $user->email ?? '-' }}</td>
                        <td class="py-3 px-4 font-medium text-sm text-base-content/70">{{ $user->nim ?? '-' }}</td>
                        <td class="py-3 px-4 font-medium text-sm text-base-content/70 capitalize">{{ $user->role }}
                        </td>
                        <td class="py-3 px-4 font-medium text-sm">
                            @if (!$user->is_first_login)
                                <span class="text-fern-700 font-semibold">Aktif</span>
                            @else
                                <span class="text-red-500 font-semibold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex gap-2">
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST"
                                    class="m-0 p-0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-xs {{ !$user->is_first_login ? 'bg-orange-100 hover:bg-orange-200 text-orange-700' : 'bg-fern-100 hover:bg-fern-200 text-fern-700' }} border-none rounded-md font-bold">
                                        {{ !$user->is_first_login ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                    class="btn btn-xs bg-amber-500 hover:bg-amber-600 text-white border-none rounded-md font-bold">
                                    Edit
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                    class="m-0 p-0"
                                    onsubmit="confirmAction(event, 'Yakin ingin menghapus pengguna ini?', true);">
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
                        <td colspan="7" class="py-8 text-center text-sm font-medium text-base-content/60">Tidak ada
                            pengguna yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
