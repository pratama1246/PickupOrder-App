@props([
    'role' => 'user'
])

<div class="dock lg:hidden bg-base-100 shadow-lg border-t border-base-200 z-50">
  @if($role === 'admin')
    <a href="/admin/dashboard" class="flex flex-col items-center gap-0.5 {{ request()->is('admin/dashboard') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="14" width="7" height="7" rx="1.5" />
        <rect x="3" y="14" width="7" height="7" rx="1.5" />
      </svg>
      <span class="dock-label text-xs">Overview</span>
    </a>

    <a href="/admin/kantin" class="flex flex-col items-center gap-0.5 {{ request()->is('admin/kantin*') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
        <polyline points="9 22 9 12 15 12 15 22" />
      </svg>
      <span class="dock-label text-xs">Kantin</span>
    </a>

    <a href="/admin/pengguna" class="flex flex-col items-center gap-0.5 {{ request()->is('admin/pengguna*') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
        <circle cx="9" cy="7" r="4" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
      </svg>
      <span class="dock-label text-xs">Pengguna</span>
    </a>
  @elseif($role === 'vendor')
    <a href="/vendor/dashboard" class="flex flex-col items-center gap-0.5 {{ request()->is('vendor/dashboard') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="3" width="7" height="7" rx="1.5" />
        <rect x="14" y="14" width="7" height="7" rx="1.5" />
        <rect x="3" y="14" width="7" height="7" rx="1.5" />
      </svg>
      <span class="dock-label text-xs">Overview</span>
    </a>

    <a href="/vendor/order" class="flex flex-col items-center gap-0.5 {{ request()->is('vendor/order*') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span class="dock-label text-xs">Order</span>
    </a>

    <a href="/vendor/menu" class="flex flex-col items-center gap-0.5 {{ request()->is('vendor/menu*') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
      </svg>
      <span class="dock-label text-xs">Menu</span>
    </a>

    <a href="/vendor/laporan" class="flex flex-col items-center gap-0.5 {{ request()->is('vendor/laporan*') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
      </svg>
      <span class="dock-label text-xs">Laporan</span>
    </a>
  @else
    <a href="/" class="flex flex-col items-center gap-0.5 {{ request()->is('/') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><polyline points="1 11 12 2 23 11" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline><path d="m5,13v7c0,1.105.895,2,2,2h10c1.105,0,2-.895,2-2v-7" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path><line x1="12" y1="22" x2="12" y2="18" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></line></g></svg>
      <span class="dock-label text-xs">Beranda</span>
    </a>

    <a href="/pesan" class="flex flex-col items-center gap-0.5 {{ request()->is('pesan*') || request()->is('kantin*') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2M7 2v20M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7" />
      </svg>
      <span class="dock-label text-xs">Pesan</span>
    </a>

    <a href="/riwayat" class="flex flex-col items-center gap-0.5 {{ request()->is('riwayat*') ? 'dock-active text-fern-700 font-bold' : '' }}">
      <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
        <path d="M3 3v5h5" />
        <path d="M12 7v5l4 2" />
      </svg>
      <span class="dock-label text-xs">Riwayat</span>
    </a>
  @endif
</div>