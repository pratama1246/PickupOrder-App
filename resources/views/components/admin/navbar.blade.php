<header class="navbar bg-shadow-grey-900 h-20 flex items-center justify-between px-4 sm:px-6 shrink-0 z-50 shadow-md">
    <div class="flex items-center gap-3 sm:gap-4">
        <div class="bg-fern-700 text-white font-bold text-lg px-3 py-1 rounded-lg tracking-wide shrink-0">
            LOGO
        </div>
        <span class="text-white/90 font-bold text-base sm:text-lg tracking-wide">Admin <span class="max-[380px]:hidden">Dashboard</span></span>
    </div>

    <div class="flex items-center gap-3">
        <div class="dropdown dropdown-end ml-1">
          <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
            @if(auth()->check() && auth()->user()->avatar)
            <div class="w-8 rounded-full ring-2 ring-fern-200">
              <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar" class="object-cover" />
            </div>
            @else
            <div class="bg-fern-100 text-fern-700 w-8 rounded-full ring-2 ring-fern-200 flex items-center justify-center">
              <span class="text-sm font-bold uppercase">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
            </div>
            @endif
          </div>
          <ul tabindex="0" class="menu menu-md dropdown-content bg-base-100 rounded-box z-10 mt-3 w-56 p-2 shadow-lg border border-base-200 text-base-content">
            <li><a href="{{ route('home') }}" class="font-medium">Halaman Utama</a></li>
            <li><a href="{{ route('profile.edit') }}" class="font-medium">Profil dan Pengaturan</a></li>
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
