{{-- 
  Layout Utama Autentikasi (Sisi Mahasiswa, Vendor, Admin):
  - Menyediakan tampilan minimalis, modern, dan interaktif untuk halaman Login, Registrasi, dll.
  - Memuat x-toast notifikasi untuk menampilkan validasi/gagal login secara instan dari backend.
--}}
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login - PNC')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

    {{-- 
      Menggunakan Alpine.js untuk menganimasikan 4 blob warna dekoratif di latar belakang secara dinamis.
      Setiap 4 detik, koordinat x dan y acak baru dihasilkan untuk memicu pergerakan transisi CSS 
      yang menghasilkan efek gradien bergerak (animated mesh gradient) yang mulus di sisi klien.
    --}}
    <body class="min-h-screen bg-base-200 flex items-center justify-center p-4 relative overflow-hidden"
    x-data="{
        blobs: [
            { x: 20, y: 30, color: 'bg-fern-300' },
            { x: 80, y: 70, color: 'bg-green-200' },
            { x: 50, y: 50, color: 'bg-yellow-200' },
            { x: 30, y: 80, color: 'bg-cyan-200' }
        ],
        init() {
            setInterval(() => {
                this.blobs[0].x = 10 + Math.random() * 40;
                this.blobs[0].y = 10 + Math.random() * 80;
    
                this.blobs[1].x = 50 + Math.random() * 40;
                this.blobs[1].y = 10 + Math.random() * 80;
    
                this.blobs[2].x = 30 + Math.random() * 40;
                this.blobs[2].y = 30 + Math.random() * 40;
    
                this.blobs[3].x = 20 + Math.random() * 50;
                this.blobs[3].y = 50 + Math.random() * 40;
            }, 4000);
        }
    }">

    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden hidden md:block">
        <template x-for="(blob, index) in blobs" :key="index">
            <div class="absolute w-[20rem] md:w-140 h-80 md:h-140 rounded-full mix-blend-multiply opacity-50 md:opacity-40 transition-all duration-4000 ease-in-out"
                :class="blob.color"
                :style="`left: ${blob.x}%; top: ${blob.y}%; transform: translate(-50%, -50%); filter: blur(80px);`">
            </div>
        </template>
    </div>
    
    <div
        class="w-full max-w-4xl rounded-2xl overflow-hidden shadow-md flex flex-col md:flex-row min-h-fit md:min-h-[600px] relative z-10 bg-fern-50">

        <!-- Sidebar/Header Hero (Gelap) - Membentang Penuh di Mobile, Menjadi Sidebar di Desktop -->
        <div class="bg-shadow-grey-900 text-white p-5 md:p-10 flex flex-row md:flex-col justify-between items-center md:items-start md:w-5/12 shrink-0">
            <!-- Brand & Logo -->
            <div class="flex items-center gap-3 md:block w-full">
                <!-- Tampilan Mobile (Horizontal Row Banner) -->
                <div class="flex items-center gap-3 md:hidden">
                    <img src="{{ asset('assets/illustration/logo-pnc.jpg') }}" alt="Logo PNC"
                        class="w-9 h-9 object-contain rounded-lg bg-white p-0.5 shadow-sm"
                        onerror="this.src='https://ui-avatars.com/api/?name=PNC&color=7F9CF5&background=EBF4FF'">
                    <div>
                        <h2 class="text-xs font-bold text-white leading-tight">Sistem Pickup Order PNC</h2>
                        <p class="text-[10px] text-white/60 font-medium">Politeknik Negeri Cilacap</p>
                    </div>
                </div>

                <!-- Tampilan Desktop (Badge + Tagline) -->
                <div class="hidden md:block">
                    <div
                        class="inline-block bg-brand-gradient text-white px-4 py-1.5 rounded-md text-xs font-bold shadow-sm mb-4">
                        Sistem Pickup Order PNC
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold leading-tight text-white">
                        @yield('hero-title', 'Pesan Makanan Langsung, Tanpa Perlu Ke kantin')
                    </h1>
                </div>
            </div>

            <!-- Footer: Made for PNC (Hanya Muncul di Desktop) -->
            <div class="hidden md:block mt-8 md:mt-0">
                <div class="bg-white text-base-content rounded-xl px-4 py-3 flex items-center gap-1.5 w-fit">
                    <span class="font-bold text-sm">Made From and For</span>
                    <span class="text-2xl flex items-center">
                        <img src="{{ asset('assets/illustration/logo-pnc.jpg') }}" alt="Logo PNC"
                            class="w-7 h-7 object-contain rounded-md"
                            onerror="this.src='https://ui-avatars.com/api/?name=PNC&color=7F9CF5&background=EBF4FF'">
                    </span>
                </div>
            </div>
        </div>

        <!-- Form Container (Responsif) -->
        <div class="bg-fern-50 flex-1 flex items-center justify-center p-8 sm:p-10">
            <div class="w-full max-w-sm">
                @yield('form')
            </div>
        </div>

    </div>

    <x-toast />
</body>

</html>
