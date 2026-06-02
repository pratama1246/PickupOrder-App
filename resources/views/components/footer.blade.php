{{-- 
  Komponen Footer Global:
  - Menyediakan navigasi penutup yang responsive (menggunakan kelas DaisyUI 'footer sm:footer-horizontal').
  - Memetakan tautan navigasi utama menggunakan named route Laravel (route('home'), route('about'), dll.) untuk fleksibilitas URL.
  - Memuat informasi operasional (jam buka, peta lokasi, kontak bantuan) serta hak cipta dinamis (tahun saat ini via helper date()).
--}}
<footer class="bg-base-200 text-base-content mt-12">
    <div class="footer sm:footer-horizontal p-10 px-6 sm:px-10 md:px-16 lg:px-24">

        <aside>
            <div class="bg-fern-700 text-white font-bold text-lg px-3 py-2 rounded-lg tracking-wide w-fit mb-2">
                LOGO
            </div>
            <p class="text-sm text-base-content/70 max-w-xs leading-relaxed">
                Sistem Pickup Order Politeknik Negeri Cilacap. Pesan makanan kantin tanpa antri, langsung dari
                genggamanmu.
            </p>
            <div class="mt-4">
                <p class="text-xs font-bold text-fern-700 uppercase tracking-wider mb-2">Metode Pembayaran</p>
                <div class="flex items-center gap-2 flex-wrap">
                    <span
                        class="text-xs bg-base-100 border border-base-300 rounded-md px-2 py-1 font-medium text-base-content/80">Tunai</span>
                    <span
                        class="text-xs bg-base-100 border border-base-300 rounded-md px-2 py-1 font-medium text-base-content/80">Transfer Bank</span>
                    <span
                        class="text-xs bg-base-100 border border-base-300 rounded-md px-2 py-1 font-medium text-base-content/80">QRIS</span>
                </div>
            </div>
        </aside>

        <nav>
            <h6 class="footer-title text-fern-700">Navigasi</h6>
            <a href="{{ route('home') }}" class="link link-hover text-sm">Beranda</a>
            <a href="{{ route('canteen.index') }}" class="link link-hover text-sm">Pesan Makanan</a>
            @if(!auth()->check() || auth()->user()->role === 'mahasiswa')
            <a href="{{ route('order.index') }}" class="link link-hover text-sm">Riwayat Pesanan</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="link link-hover text-sm">Profil Saya</a>
            <a href="{{ route('about') }}" class="link link-hover text-sm">Tentang Kami</a>
        </nav>

        <nav>
            <h6 class="footer-title text-fern-700">Informasi Kantin</h6>

            <div class="flex items-start gap-2 text-sm text-base-content/80">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 shrink-0 text-fern-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-base-content">Jam Operasional</p>
                    <p>Senin - Jumat: 07.00 - 15.00</p>
                    <p class="text-base-content/60 text-xs mt-0.5">Sabtu - Minggu: Tutup</p>
                </div>
            </div>

            <div class="flex items-start gap-2 text-sm text-base-content/80 mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 shrink-0 text-fern-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 0 1-2.827 0l-4.244-4.243a8 8 0 1 1 11.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-base-content">Lokasi Kantin</p>
                    <p>Gedung Kantin PNC, Jl. Dr. Soetomo No.1</p>
                    <p class="text-base-content/60 text-xs mt-0.5">Sidakaya, Cilacap Selatan</p>
                </div>
            </div>

            <div class="flex items-start gap-2 text-sm text-base-content/80 mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-0.5 shrink-0 text-fern-600" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />
                </svg>
                <div>
                    <p class="font-semibold text-base-content">Bantuan & Kontak</p>
                    <p>Admin Platform PNC</p>
                    <p class="text-base-content/60 text-xs mt-0.5">pnc.pickuporder@gmail.com</p>
                </div>
            </div>
        </nav>

    </div>

    <div class="footer footer-center p-4 bg-base-300 border-t border-base-300">
        <aside>
            <p class="text-sm">Copyright &copy; {{ date('Y') }} Sistem Pickup Order - Politeknik Negeri Cilacap. All
                rights reserved.</p>
        </aside>
    </div>
</footer>
