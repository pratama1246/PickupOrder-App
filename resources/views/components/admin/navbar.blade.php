{{-- 
  Komponen Navbar Dashboard Admin:
  - Menyusun bagian header atas khusus untuk panel navigasi peran Administrator.
  - Memuat avatar admin dengan pencarian data dinamis serta fallback ke inisial nama berlatar belakang token warna fern jika kosong.
  - Menyediakan dropdown pengaturan akun, navigasi kembali ke halaman utama publik, 
    dan tombol keluar terproteksi CSRF form POST.
--}}
<header class="navbar bg-fern-50 border-b border-fern-200 h-20 flex items-center justify-between px-3 sm:px-6 shrink-0 z-50">
    <div class="flex items-center gap-3 sm:gap-4">
        <a href="{{ route('admin.dashboard') }}" class="group inline-flex items-center transition active:scale-95 shrink-0" aria-label="PickupOrder PNC Admin">
            <x-brand-logo variant="light" size="sm" />
        </a>
        <div class="h-5 w-px bg-fern-300 hidden sm:block"></div>
        <span class="text-fern-950 font-bold text-base sm:text-lg tracking-wide">Admin <span
                class="max-[380px]:hidden text-fern-700">Dashboard</span></span>
    </div>

    <div class="flex items-center gap-3">
        <div class="dropdown dropdown-end ml-1">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                @if (auth()->check() && auth()->user()->avatar)
                    <div class="w-8 rounded-full ring-2 ring-fern-200">
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar"
                            class="object-cover" />
                    </div>
                @else
                    <div
                        class="bg-fern-100 text-fern-700 w-8 rounded-full ring-2 ring-fern-200 flex items-center justify-center">
                        <span class="text-sm font-bold uppercase">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <ul tabindex="0"
                class="menu menu-md dropdown-content bg-base-100 rounded-box z-10 mt-3 w-64 p-2 shadow-lg border border-base-200 text-base-content">
                <div class="px-4 py-2.5 border-b border-base-200 mb-1 min-w-0">
                    <div class="text-[10px] font-bold text-fern-700 uppercase tracking-wider">Administrator</div>
                    <div class="text-sm font-bold text-base-content truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-base-content/50 truncate">{{ auth()->user()->email }}</div>
                </div>
                <li><a href="{{ route('home') }}" class="font-medium">Halaman Utama</a></li>
                <li><a href="{{ route('profile.edit') }}" class="font-medium">Pengaturan Akun</a></li>
                <li class="border-t border-base-200 my-1"></li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-error font-medium">Keluar</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
