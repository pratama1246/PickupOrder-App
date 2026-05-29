<aside class="w-64 h-full bg-shadow-grey-900 flex flex-col shrink-0 overflow-y-auto">
    <nav class="flex-1 px-3 py-4 space-y-1">

        <a href="/admin/dashboard"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('admin/dashboard') ? 'bg-fern-700 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="14" width="7" height="7" rx="1.5" />
                <rect x="3" y="14" width="7" height="7" rx="1.5" />
            </svg>
            Overview
        </a>

        <a href="/admin/kantin"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('admin/kantin*') ? 'bg-fern-700 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            Kantin
        </a>

        <a href="/admin/pengguna"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('admin/pengguna*') ? 'bg-fern-700 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
            </svg>
            Pengguna
        </a>
    </nav>

    <div class="p-3 border-t border-white/10 mt-auto">
        <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm text-red-400 hover:bg-white/10 hover:text-red-300 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Keluar
        </a>
        <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</aside>
