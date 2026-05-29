@extends('layouts.app')

@section('title', 'Tentang Kami - Pickup Order PNC')

@section('content')
    <x-breadcrumb :links="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Tentang Kami']
    ]" />

    <div class="px-4 sm:px-10 md:px-16 lg:px-24 pb-16 pt-8">
        <div class="max-w-4xl mx-auto space-y-12">
            
            <section>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-base-content mb-4">
                    Tentang Pickup Order PNC
                </h1>
                <p class="text-base-content/80 font-medium leading-relaxed">
                    Pickup Order PNC adalah platform yang dibuat khusus untuk warga kampus Politeknik Negeri Cilacap. Kami membantu mahasiswa, dosen, dan staf untuk memesan makanan dari kantin kampus secara online, sehingga tidak perlu lagi berlama-lama antre di depan warung.
                </p>
            </section>

            <section class="bg-vanilla-custard-50 rounded-3xl p-6 sm:p-8 border border-base-content/10 shadow-sm">
                <h2 class="text-2xl font-bold text-base-content mb-3">Mengapa Platform Ini Dibuat</h2>
                <p class="text-base-content/80 font-medium leading-relaxed">
                    Waktu istirahat di kampus cukup singkat. Seringkali, mahasiswa menghabiskan banyak waktu hanya untuk antre memesan makanan. Di sisi lain, pemilik kantin juga kewalahan melayani pembeli di jam-jam sibuk. Kami membangun sistem ini agar proses pemesanan jadi lebih teratur, dan waktu istirahat bisa benar-benar dipakai untuk makan atau beristirahat dengan tenang.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-base-content mb-5">Cara Kerja</h2>
                <div class="space-y-5">
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-fern-100 text-fern-700 flex items-center justify-center shrink-0 font-bold text-lg">1</div>
                        <div class="mt-1">
                            <h3 class="font-bold text-base-content text-lg">Pesan Makanan</h3>
                            <p class="text-sm sm:text-base text-base-content/70 mt-1 font-medium">Pilih kantin dan menu yang tersedia melalui HP kamu di mana saja.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-fern-100 text-fern-700 flex items-center justify-center shrink-0 font-bold text-lg">2</div>
                        <div class="mt-1">
                            <h3 class="font-bold text-base-content text-lg">Tunggu Proses</h3>
                            <p class="text-sm sm:text-base text-base-content/70 mt-1 font-medium">Kantin menerima pesananmu secara digital dan mulai memasak.</p>
                        </div>
                    </div>
                    <div class="flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-fern-100 text-fern-700 flex items-center justify-center shrink-0 font-bold text-lg">3</div>
                        <div class="mt-1">
                            <h3 class="font-bold text-base-content text-lg">Ambil Pesanan</h3>
                            <p class="text-sm sm:text-base text-base-content/70 mt-1 font-medium">Datang langsung ke kantin dan ambil makananmu saat status pesanan sudah siap.</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <section class="bg-base-100 border border-base-200 rounded-3xl p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-base-content mb-4">Manfaat Bagi Mahasiswa</h2>
                    <ul class="space-y-3">
                        <li class="flex gap-3 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-fern-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm sm:text-base font-medium text-base-content/80 leading-snug">Tidak perlu berdesakan saat jam istirahat.</span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-fern-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm sm:text-base font-medium text-base-content/80 leading-snug">Tahu persis kapan pesanan makanan siap diambil.</span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-fern-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm sm:text-base font-medium text-base-content/80 leading-snug">Bisa mengecek menu apa saja yang masih ada tanpa harus ke lokasi.</span>
                        </li>
                    </ul>
                </section>

                <section class="bg-base-100 border border-base-200 rounded-3xl p-6 sm:p-8 shadow-sm">
                    <h2 class="text-xl font-bold text-base-content mb-4">Manfaat Bagi Kantin</h2>
                    <ul class="space-y-3">
                        <li class="flex gap-3 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-fern-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm sm:text-base font-medium text-base-content/80 leading-snug">Pesanan tercatat dengan jelas, mengurangi kemungkinan salah buat menu.</span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-fern-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm sm:text-base font-medium text-base-content/80 leading-snug">Tidak pusing mengatur kerumunan antrean panjang di depan warung.</span>
                        </li>
                        <li class="flex gap-3 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-fern-700 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm sm:text-base font-medium text-base-content/80 leading-snug">Bisa mengatur stok sehingga pembeli tidak kecewa memesan menu yang sudah habis.</span>
                        </li>
                    </ul>
                </section>
            </div>

            <section class="bg-emerald-50 rounded-3xl p-6 sm:p-8 border border-emerald-100">
                <h2 class="text-2xl font-bold text-base-content mb-3">Tujuan Pengembangan Platform</h2>
                <p class="text-base-content/80 font-medium leading-relaxed">
                    Tujuan kami sederhana: membuat aktivitas makan di kantin jadi lebih gampang. Kami ingin membantu bapak/ibu kantin berjualan dengan lebih rapi, sekaligus memberikan kenyamanan buat mahasiswa yang butuh makanan cepat di jeda pergantian mata kuliah. Ini murni untuk kemudahan internal kampus PNC dan perbaikan fasilitas bersama.
                </p>
            </section>
            
        </div>
    </div>
@endsection
