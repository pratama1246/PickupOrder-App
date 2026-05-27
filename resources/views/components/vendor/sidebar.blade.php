<aside class="w-64 h-full bg-shadow-grey-900 flex flex-col shrink-0 overflow-y-auto">
    <nav class="flex-1 px-3 py-4 space-y-1">

        <a href="/vendor/dashboard"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('vendor/dashboard') ? 'bg-fern-700 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="14" width="7" height="7" rx="1.5" />
                <rect x="3" y="14" width="7" height="7" rx="1.5" />
            </svg>
            Overview
        </a>

        <a href="/vendor/order"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('vendor/order*') ? 'bg-fern-700 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Order
        </a>


        <a href="/vendor/menu"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('vendor/menu*') ? 'bg-fern-700 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            Menu
        </a>
    </nav>

    <div class="p-3 border-t border-white/10 mt-auto">
        <a href="#" onclick="event.preventDefault(); document.getElementById('vendor-logout-form').submit();"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm text-red-400 hover:bg-white/10 hover:text-red-300 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Keluar
        </a>
        <form id="vendor-logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</aside>
