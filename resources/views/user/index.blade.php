@extends('layouts.app')

@section('content')

    <section class="px-6 sm:px-10 md:px-16 lg:px-24 mt-4 mb-4">
        <div class="max-w-8xl mx-auto flex justify-end">
           <label class="input input-bordered flex items-center gap-2 w-full max-w-sm shadow-sm rounded-3xl input-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-base-content/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                 <input type="search" class="grow text-sm sm:text-base font-medium" placeholder="Cari menu atau kantin..." />
            </label>
        </div>
    </section>


    <section class="px-6 sm:px-10 md:px-16 lg:px-24 py-8 md:py-10">
        <div class="max-w-8xl mx-auto flex flex-col lg:flex-row items-center lg:items-start gap-10">

            <div class="flex-1 w-full text-center lg:text-left">

                <h1 class="text-4xl sm:text-5xl font-bold leading-tight mb-3 text-base-content">
                    Laper Abis 
                    <br class="block sm:hidden" />
                    <span id="typing-text" class="text-emerald-400">Nugas?</span>
                </h1>

                <h2 class="text-xl sm:text-4xl font-semibold mb-4 text-base-content leading-relaxed sm:leading-normal">
                    Langsung Order Makanan
                    <br class="block sm:hidden" />
                    <span class="inline-block bg-emerald-400 text-white px-3 py-1.5 sm:py-1 rounded-lg text-lg sm:text-4xl font-semibold mt-2 sm:mt-0 sm:ml-1">
                        Tanpa Perlu Ke Kantin
                    </span>
                </h2>

                <p class="text-sm sm:text-base text-base-content/70 font-medium max-w-md mx-auto lg:mx-0 mb-8 leading-relaxed">
                    Platform pemesanan makanan kampus yang cepat, efisien, dan dirancang untuk seluruh Civitas Akademik Politeknik Negeri Cilacap.
                </p>

                <div class="flex flex-col gap-3 justify-center lg:justify-start items-center lg:items-start">
                    <button class="btn bg-fern-700 text-white hover:bg-fern-800 border-none shadow-md px-6 py-2 min-h-0 h-auto text-sm rounded-2xl w-52 transition-all duration-200 active:scale-95 hover:shadow-lg">
                        Pesan Sekarang
                    </button>
                    <button class="btn bg-vanilla-custard-300 text-black hover:bg-fern-700 hover:text-white border-none shadow-md px-6 py-2 min-h-0 h-auto text-sm rounded-2xl w-52 transition-all duration-200 active:scale-95 hover:shadow-lg">
                        Lihat Menu
                    </button>
                </div>
            </div>

            <div class="flex-1 w-full flex justify-center lg:justify-end">
                <div class="w-[350px] h-[350px] sm:w-[450px] sm:h-[450px] md:w-[520px] md:h-[520px] lg:w-[580px] lg:h-[580px] max-w-full overflow-hidden flex items-center justify-center">
                    <img
                        src="{{ asset('assets/illustration/Eating%20healthy%20food-cuate%20(1).svg') }}"
                        alt="Ilustrasi Pesan Makanan"
                        class="w-full h-full object-contain"
                    />
                </div>
            </div>

        </div>
    </section>

    <section class="px-6 sm:px-10 md:px-16 lg:px-24 py-10">
        <div class="max-w-8xl mx-auto">

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-base-content">Menu Populer!</h2>
                <p class="text-base-content/60 text-sm mt-1">Menu Yang Lagi Banyak Di Pesan Seluruh Penghuni Kampus!</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse ($popularMenus as $menu)
                    <x-foodcard :id="$menu->id" :name="$menu->name" :canteenName="$menu->canteen->name" :description="$menu->description" :price="$menu->formatted_price"
                        :image="$menu->image ? asset('storage/' . $menu->image) : null" rating="4.8" :actionUrl="route('menu.show', ['canteenId' => $menu->canteen_id, 'id' => $menu->id])" />
                @empty
                    <p class="text-base-content/60">Belum ada menu populer.</p>
                @endforelse
            </div>

        </div>
    </section>

    <section class="px-6 sm:px-10 md:px-16 lg:px-24 py-10">
        <div class="max-w-8xl mx-auto">

            <div class="mb-6">
                <h2 class="text-2xl font-bold text-base-content">Pilih Kantin!</h2>
                <p class="text-base-content/60 text-sm mt-1">Lihat menu yang tersedia di setiap kantin</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse ($canteens as $canteen)
                    <x-canteencard :id="$canteen->id" :name="$canteen->name" :image="$canteen->image ? asset('storage/' . $canteen->image) : null" :description="$canteen->description ?? 'Kantin pilihan mahasiswa.'"
                        :menuCount="$canteen->available_menus_count" :actionUrl="route('canteen.show', $canteen->id)" actionText="Lihat Kantin" />
                @empty
                    <p class="text-base-content/60">Belum ada kantin yang buka saat ini.</p>
                @endforelse
            </div>

        </div>
    </section>

@endsection

@push('scripts')
<script>
    const words = ["Nugas?", "Praktikum?", "Ngoding?", "Kelas?", "Begadang?"];
    let i = 0;
    let j = 0;
    let currentWord = "";
    let isDeleting = false;
 
    function typeEffect() {
        currentWord = words[i];
 
        if (isDeleting) {
            j--;
        } else {
            j++;
        }
 
        document.getElementById("typing-text").textContent =
            currentWord.substring(0, j);
 
        let speed = isDeleting ? 50 : 100;
 
        if (!isDeleting && j === currentWord.length) {
            speed = 1200;
            isDeleting = true;
        } else if (isDeleting && j === 0) {
            isDeleting = false;
            i = (i + 1) % words.length;
            speed = 300;
        }
 
        setTimeout(typeEffect, speed);
    }
 
    typeEffect();
</script>
@endpush