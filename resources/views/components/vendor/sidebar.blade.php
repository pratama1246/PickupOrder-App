{{-- 
  Komponen Sidebar Dashboard Vendor:
  - Menyediakan navigasi menu samping persisten untuk layout panel administrasi pemilik kantin (Vendor).
  - Mengevaluasi jalur rute aktif secara dinamis menggunakan helper 'request()->is()' untuk 
    menerapkan kelas gaya penanda rute terpilih secara visual.
  - Mengintegrasikan tombol logout aman di bagian bawah sidebar dengan pemicu form POST ('vendor-logout-form') 
    guna melindungi proses keluar dari serangan CSRF.
--}}
<aside class="w-64 h-full bg-fern-50 border-r border-fern-200 flex flex-col shrink-0 overflow-y-auto">
    <nav class="flex-1 px-3 py-4 space-y-1">

        <a href="{{ route('vendor.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('vendor/dashboard') ? 'bg-fern-700 text-white shadow-sm' : 'text-fern-900/75 hover:bg-fern-100/70 hover:text-fern-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="3" width="7" height="7" rx="1.5" />
                <rect x="14" y="14" width="7" height="7" rx="1.5" />
                <rect x="3" y="14" width="7" height="7" rx="1.5" />
            </svg>
            Overview
        </a>

        <a href="{{ route('vendor.order.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('vendor/order*') ? 'bg-fern-700 text-white shadow-sm' : 'text-fern-900/75 hover:bg-fern-100/70 hover:text-fern-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Order
        </a>


        <a href="{{ route('vendor.menu.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('vendor/menu*') ? 'bg-fern-700 text-white shadow-sm' : 'text-fern-900/75 hover:bg-fern-100/70 hover:text-fern-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            Menu
        </a>

        <a href="{{ route('vendor.report.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm transition-colors
       {{ request()->is('vendor/report*') ? 'bg-fern-700 text-white shadow-sm' : 'text-fern-900/75 hover:bg-fern-100/70 hover:text-fern-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Laporan
        </a>
    </nav>

    <div class="p-3 border-t border-fern-200 mt-auto">
        <a href="#" onclick="event.preventDefault(); document.getElementById('vendor-logout-form').submit();"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium text-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors">
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
