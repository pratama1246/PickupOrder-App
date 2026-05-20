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
      <li><a href="/" class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium {{ request()->is('/') ? 'bg-fern-50 text-fern-700 font-medium' : 'text-white' }}">Beranda</a></li>
      <li><a href="/pesan" class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium {{ request()->is('pesan*') || request()->is('kantin*') ? 'bg-fern-50 text-fern-700 font-medium' : 'text-white' }}">Pesan</a></li>
      <li><a href="/riwayat" class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium {{ request()->is('riwayat*') ? 'bg-fern-50 text-fern-700 font-medium' : 'text-white' }}">Riwayat</a></li>
      <li><a href="#" class="rounded-lg hover:bg-fern-50 hover:text-fern-700 font-medium text-white">Tentang Kami</a></li>
    </ul>
  </div>

  <div class="navbar-end gap-1">

    @php
        $cartCount = count(session('cart', []));
    @endphp
    <a href="/keranjang" class="btn btn-ghost btn-circle hover:bg-transparent">
      <div class="indicator">

        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24">
          <path fill="white" d="M17 18c-1.11 0-2 .89-2 2a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2M1 2v2h2l3.6 7.59l-1.36 2.45c-.15.28-.24.61-.24.96a2 2 0 0 0 2 2h12v-2H7.42a.25.25 0 0 1-.25-.25q0-.075.03-.12L8.1 13h7.45c.75 0 1.41-.42 1.75-1.03l3.58-6.47c.07-.16.12-.33.12-.5a1 1 0 0 0-1-1H5.21l-.94-2M7 18c-1.11 0-2 .89-2 2a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2"/>
        </svg>

        @if($cartCount > 0)
        <span class="badge badge-sm indicator-item bg-fern-700 text-white border-0 font-bold">
            {{ $cartCount }}
        </span>
        @endif

      </div>
    </a>

    <div class="dropdown dropdown-end ml-1">
      <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
        <div class="w-8 rounded-full ring-2 ring-fern-200">
          <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" alt="avatar" />
        </div>
      </div>
      <ul tabindex="0" class="menu menu-md dropdown-content bg-base-100 rounded-box z-10 mt-3 w-56 p-2 shadow-lg border border-base-200">
        <li><a class="justify-between font-medium">Profil <span class="badge badge-sm bg-fern-100 text-fern-700 border-0 font-medium">Baru</span></a></li>
        <li><a class="font-medium">Pengaturan</a></li>
        <li>
          <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-error font-medium">Keluar</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
              @csrf
          </form>
        </li>
      </ul>
    </div>

  </div>
</div>