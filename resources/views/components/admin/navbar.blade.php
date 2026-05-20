<header class="navbar bg-shadow-grey-900 h-16 sm:h-20 flex items-center justify-between lg:justify-end px-4 sm:px-6 shrink-0 z-50 shadow-md">
    <div class="flex items-center gap-3 sm:gap-4 lg:hidden">
        <div class="bg-fern-700 text-white font-bold text-xs px-2 py-1 rounded-md tracking-wide shrink-0">
            LOGO
        </div>
        <span class="text-white/90 font-bold text-sm sm:text-base">Admin Dashboard</span>
    </div>

    <div class="flex items-center gap-3">
        <a href="/"
            class="btn btn-sm bg-white/10 hover:bg-white/20 text-white border-none font-medium text-xs rounded-md px-2.5 sm:px-4 flex items-center gap-1.5">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="hidden sm:inline">Ke Halaman Utama</span>
        </a>
        <div class="dropdown dropdown-end ml-1">
          <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
            <div class="w-8 rounded-full ring-2 ring-fern-200">
              <img src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" alt="avatar" />
            </div>
          </div>
          <ul tabindex="0" class="menu menu-md dropdown-content bg-base-100 rounded-box z-10 mt-3 w-56 p-2 shadow-lg border border-base-200 text-base-content">
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
</header>
