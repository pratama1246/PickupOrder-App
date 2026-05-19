@extends('layouts.app')

@section('title', 'Keranjang Belanja - PNC')

@section('content')
<main class="min-h-screen bg-base-100 pb-16"
      x-data="{
          canteenItems: {
              'Nasi Rames': { qty: 2, price: 10000 },
              'Es Teh': { qty: 2, price: 3000 }
          },
          get totalQty() {
              return Object.values(this.canteenItems).reduce((sum, item) => sum + item.qty, 0);
          },
          get totalPrice() {
              return Object.values(this.canteenItems).reduce((sum, item) => sum + (item.qty * item.price), 0);
          },
          updateItem(name, qty, price) {
              if (this.canteenItems[name]) {
                  this.canteenItems[name].qty = qty;
                  localStorage.setItem('cart', JSON.stringify(this.canteenItems));
                  window.dispatchEvent(new Event('cart-updated'));
              }
          },
          removeItem(name) {
              delete this.canteenItems[name];
              this.canteenItems = { ...this.canteenItems };
              localStorage.setItem('cart', JSON.stringify(this.canteenItems));
              window.dispatchEvent(new Event('cart-updated'));
          }
      }"
      x-init="localStorage.setItem('cart', JSON.stringify(canteenItems)); window.dispatchEvent(new Event('cart-updated'))"
      x-on:cart-item-updated.window="updateItem($event.detail.name, $event.detail.qty, $event.detail.price)"
      x-on:cart-item-removed.window="removeItem($event.detail.name)"
>

    <x-breadcrumb
        class="pt-8 pb-4"
        :links="[
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Keranjang Belanja']
        ]"
    />

    <section class="px-4 sm:px-10 md:px-16 lg:px-24 pb-6">
        <div class="max-w-8xl mx-auto">
            <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Keranjang Belanja</h1>
            <p class="text-base-content/70 text-sm sm:text-base font-medium">Silahkan pilih kantin dan detail pesanan terlebih dahulu</p>
        </div>
    </section>

    <section class="px-4 sm:px-10 md:px-16 lg:px-24">
        <div class="max-w-8xl mx-auto flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">

            <div class="w-full lg:flex-1 min-w-0 space-y-5">

                <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm"
                     x-show="totalQty > 0">

                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg sm:text-xl font-bold text-base-content">Kantin 1</h2>
                        <span class="text-sm font-bold text-base-content/60"><span x-text="totalQty">4</span> Pesanan</span>
                    </div>

                    <div class="space-y-3 mb-5">

                        <x-user.cart-item
                            image="{{ asset('assets/food/Nasi Rames.jpg') }}"
                            name="Nasi Rames"
                            description="Nasi + Sayur Mi + Kering Tempe + Sayur Sawi"
                            :price="10000"
                            :quantity="2"
                        />

                        <x-user.cart-item
                            image="{{ asset('assets/food/es teh.jpg') }}"
                            name="Es Teh"
                            :price="3000"
                            :quantity="2"
                        />

                    </div>

                    <textarea
                        rows="3"
                        placeholder="Catatan untuk kantin (Opsional)"
                        class="textarea textarea-bordered w-full rounded-2xl text-sm font-medium border-base-content/20 bg-white focus:outline-none focus:border-base-content/40 resize-none placeholder:text-base-content/40"
                    ></textarea>

                </div>

                <div class="bg-white border border-base-content/20 rounded-3xl p-8 text-center shadow-sm"
                     x-show="totalQty === 0"
                     style="display: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-base-content/30 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="text-lg font-bold text-base-content mb-1">Keranjang Belanja Kosong</h3>
                    <p class="text-sm text-base-content/60 mb-5 font-medium">Anda belum menambahkan makanan atau minuman.</p>
                    <a href="/pesan" class="btn bg-fern-700 hover:bg-fern-800 text-white border-none px-6 rounded-2xl font-bold text-sm shadow-md active:scale-95 transition-all">
                        Mulai Cari Menu
                    </a>
                </div>

            </div>

            <div class="w-full lg:w-80 xl:w-96 shrink-0">
                <x-user.cart-summary
                    :canteens="[
                        ['name' => 'Kantin 1', 'itemCount' => 4, 'subtotal' => 26000],
                    ]"
                    :total="36000"
                    checkoutUrl="/checkout"
                />
            </div>

        </div>
    </section>

</main>
@endsection
