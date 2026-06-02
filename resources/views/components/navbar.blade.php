{{-- 
  Komponen Navbar Global:
  - Menyediakan navigasi atas aplikasi; bagian center menu disembunyikan di mobile ('hidden lg:flex') karena digantikan oleh x-dock.
  - Membaca jumlah barang di keranjang belanja dari session PHP untuk badge indikator khusus mahasiswa yang login.
  - Menyusun dropdown profil pengguna terotentikasi, membedakan hak akses/role (Admin, Vendor, Mahasiswa) 
    untuk mengarahkan ke dashboard yang relevan, serta menangani aksi keluar secara aman lewat POST request.
--}}
<div class="navbar bg-shadow-grey-900 shadow-sm px-6 sticky top-0 z-50 h-20">

    <div class="navbar-start">
        <a href="/" class="flex items-center gap-2 transition active:scale-95">
            <div class="bg-fern-700 text-white font-bold text-lg px-3 py-1 rounded-lg tracking-wide">
                LOGO
            </div>
        </a>
    </div>

    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1 gap-5 text-lg font-semibold">
            <li><a href="/"
                    class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium {{ request()->is('/') ? 'bg-fern-50 text-fern-700 font-medium' : 'text-white' }}">Beranda</a>
            </li>
            <li><a href="{{ route('canteen.index') }}"
                    class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium {{ request()->is('browse*') || request()->is('canteen*') ? 'bg-fern-50 text-fern-700 font-medium' : 'text-white' }}">Pesan</a>
            </li>
            @if(!auth()->check() || auth()->user()->role === 'mahasiswa')
            <li><a href="{{ route('order.index') }}"
                    class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium {{ request()->is('history*') ? 'bg-fern-50 text-fern-700 font-medium' : 'text-white' }}">Riwayat</a>
            </li>
            @endif
            <li><a href="{{ route('about') }}"
                    class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium {{ request()->routeIs('about') ? 'bg-fern-50 text-fern-700 font-medium' : 'text-white' }}">Tentang
                    Kami</a></li>
        </ul>
    </div>

    <div class="navbar-end gap-1">

        @if(!auth()->check() || auth()->user()->role === 'mahasiswa')
        @php
            $cartCount = count(session('cart', []));
            $showCartBadge = auth()->check() && auth()->user()->role === 'mahasiswa';
        @endphp
        <a href="{{ route('cart.index') }}" class="btn btn-ghost btn-circle hover:bg-transparent" id="navbar-cart-btn">
            <div class="indicator">

                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
                    <path fill="white"
                        d="M17 18c-1.11 0-2 .89-2 2a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2M1 2v2h2l3.6 7.59l-1.36 2.45c-.15.28-.24.61-.24.96a2 2 0 0 0 2 2h12v-2H7.42a.25.25 0 0 1-.25-.25q0-.075.03-.12L8.1 13h7.45c.75 0 1.41-.42 1.75-1.03l3.58-6.47c.07-.16.12-.33.12-.5a1 1 0 0 0-1-1H5.21l-.94-2M7 18c-1.11 0-2 .89-2 2a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2" />
                </svg>

                @if ($showCartBadge && $cartCount > 0)
                    <span id="navbar-cart-count" class="badge badge-sm indicator-item bg-fern-700 text-white border-0 font-bold">
                        {{ $cartCount }}
                    </span>
                @endif

            </div>
        </a>
        @endif

        @auth
            <div class="dropdown dropdown-end ml-1">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                    @if (auth()->user()->avatar)
                        <div class="w-8 rounded-full ring-2 ring-fern-200">
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar"
                                class="object-cover" />
                        </div>
                    @else
                        <div
                            class="bg-fern-100 text-fern-700 w-8 rounded-full ring-2 ring-fern-200 flex items-center justify-center">
                            <span class="text-sm font-bold uppercase">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <ul tabindex="0"
                    class="menu menu-md dropdown-content bg-base-100 rounded-box z-10 mt-3 w-64 p-2 shadow-lg border border-base-200">
                    <div class="px-4 py-2.5 border-b border-base-200 mb-1 min-w-0">
                        <div class="text-[10px] font-bold text-fern-700 uppercase tracking-wider">
                            @if (auth()->user()->isAdmin())
                                Administrator
                            @elseif(auth()->user()->isVendor())
                                Vendor
                            @else
                                Mahasiswa
                            @endif
                        </div>
                        <div class="text-sm font-bold text-base-content truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-base-content/50 truncate">{{ auth()->user()->email }}</div>
                    </div>

                    @if (auth()->user()->isAdmin())
                        <li><a href="{{ route('admin.dashboard') }}" class="font-medium">Dashboard Admin</a></li>
                    @elseif(auth()->user()->isVendor())
                        <li><a href="{{ route('vendor.dashboard') }}" class="font-medium">Dashboard Kantin</a></li>
                        <li><a href="{{ route('vendor.canteen.edit') }}" class="font-medium">Pengaturan Kantin</a></li>
                    @endif
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
        @else
            <a href="{{ route('login') }}"
                class="btn bg-fern-700 hover:bg-fern-800 text-white rounded-xl shadow-sm transition-all active:scale-95 border-none font-semibold px-4 text-sm">
                Masuk
            </a>
        @endauth

    </div>
</div>
