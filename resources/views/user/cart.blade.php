@extends('layouts.app')

@section('title', 'Keranjang Belanja - PNC')

@section('content')
    <main class="min-h-screen bg-base-100 pb-16">

        <x-breadcrumb class="pt-8 pb-4" maxWidth="max-w-7xl" :links="[['label' => 'Beranda', 'url' => '/'], ['label' => 'Keranjang Belanja']]" />

        <section class="px-3 sm:px-10 md:px-16 lg:px-24 pb-6">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl sm:text-4xl font-bold text-base-content mb-1">Keranjang Belanja</h1>
                <p class="text-base-content/70 text-sm sm:text-base font-medium">Silahkan periksa detail pesanan Anda</p>
            </div>
        </section>

        <section class="px-3 sm:px-10 md:px-16 lg:px-24" id="cart-container" x-data="{
            items: {
                @foreach ($grouped as $canteenId => $data)
                         @foreach ($data['items'] as $item)
                             '{{ $item['menu_id'] }}': {
                                 qty: {{ $item['quantity'] }},
                                 price: {{ $item['price'] }},
                                 canteenId: {{ $item['canteen_id'] }},
                                 selected: {{ $item['stock'] > 0 ? 'true' : 'false' }},
                                 stock: {{ $item['stock'] }}
                             }, @endforeach
                @endforeach
            },
            updateTimeout: null,
            toggleItem(itemId, checked) {
                if (this.items[itemId]) {
                    if (this.items[itemId].stock <= 0) {
                        this.items[itemId].selected = false;
                        return;
                    }
                    this.items[itemId].selected = checked;
                }
            },
            toggleAll(canteenId, checked) {
                for (let id in this.items) {
                    if (this.items[id].canteenId === canteenId) {
                        if (this.items[id].stock <= 0) {
                            this.items[id].selected = false;
                            const cb = document.querySelector(`input[name='selected_menu_ids[]'][value='${id}']`);
                            if (cb) cb.checked = false;
                            continue;
                        }
                        this.items[id].selected = checked;
                        // Menyelaraskan keadaan checked state checkbox input tersembunyi dengan data state Alpine
                        const cb = document.querySelector(`input[name='selected_menu_ids[]'][value='${id}']`);
                        if (cb) cb.checked = checked;
                    }
                }
            },
            isAllSelected(canteenId) {
                const itemsInCanteen = Object.values(this.items).filter(i => i.canteenId === canteenId);
                const checkableItems = itemsInCanteen.filter(i => i.stock > 0);
                if (checkableItems.length === 0) return false;
                return checkableItems.every(i => i.selected);
            },
            changeQty(itemId, amount) {
                let item = this.items[itemId];
                if (!item) return;
                let newQty = item.qty + amount;
                if (newQty < 1) newQty = 1;
                if (newQty > 20) newQty = 20;
                if (newQty === item.qty) return;
        
                item.qty = newQty;
        
                // Debounce sync ke server sebesar 400ms untuk mencegah spam request ketika user mengubah kuantitas secara cepat
                clearTimeout(this.updateTimeout);
                this.updateTimeout = setTimeout(() => {
                    this.syncWithBackend(itemId, item.qty);
                }, 400);
            },
            async syncWithBackend(itemId, qty) {
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PUT');
                formData.append('quantity', qty);
        
                try {
                    await fetch(`/cart/${itemId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                } catch (err) {
                    console.error('Gagal melakukan sync kuantitas ke backend:', err);
                }
            },
            getCanteenTotal(canteenId) {
                let total = 0;
                for (let id in this.items) {
                    if (this.items[id].canteenId === canteenId && this.items[id].selected) {
                        total += this.items[id].qty * this.items[id].price;
                    }
                }
                return total;
            },
            getGrandTotal() {
                let total = 0;
                for (let id in this.items) {
                    if (this.items[id].selected) {
                        total += this.items[id].qty * this.items[id].price;
                    }
                }
                return total;
            }
        }">

            <form id="checkout-prepare-form" action="{{ route('checkout.prepare') }}" method="POST" class="hidden">
                @csrf
            </form>

            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
                <div class="w-full lg:flex-1 min-w-0 space-y-5">
                    @forelse ($grouped as $canteenId => $data)
                        <div class="bg-vanilla-custard-50 border border-base-content/20 rounded-3xl p-5 sm:p-6 shadow-sm">

                            <div class="flex items-center justify-between mb-5">
                                <div class="flex items-center gap-3">
                                    <label class="flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked
                                            class="checkbox checkbox-sm rounded-md border-base-content/30 checked:bg-fern-700 checked:border-fern-700 checked:text-white focus:ring-0"
                                            @change="toggleAll({{ $canteenId }}, $event.target.checked)"
                                            :checked="isAllSelected({{ $canteenId }})"
                                        >
                                    </label>
                                    <h2 class="text-lg sm:text-xl font-bold text-base-content">{{ $data['canteen_name'] }}</h2>
                                </div>
                                <span class="text-sm font-bold text-base-content/60"><span>{{ count($data['items']) }}</span>
                                    Pesanan</span>
                            </div>

                            <div class="space-y-3 mb-5">
                                @foreach ($data['items'] as $item)
                                    <x-user.cart-item
                                        :itemId="$item['menu_id']"
                                        :cartId="$item['menu_id']"
                                        :image="$item['image'] ? asset('storage/' . $item['image']) : 'https://ui-avatars.com/api/?name=' . urlencode($item['name']) . '&background=random'"
                                        :name="$item['name']"
                                        :description="$item['description'] ?? null"
                                        :price="$item['price']"
                                        :quantity="$item['quantity']"
                                        :stock="$item['stock']"
                                    />
                                @endforeach
                            </div>

                            <textarea name="notes[{{ $canteenId }}]" form="checkout-prepare-form" rows="2"
                                placeholder="Catatan untuk kantin {{ $data['canteen_name'] }} (Opsional)"
                                class="textarea textarea-bordered w-full rounded-2xl text-sm font-medium border-base-content/20 bg-white focus:outline-none focus:border-base-content/40 resize-none placeholder:text-base-content/40">{{ session('checkout_notes')[$canteenId] ?? '' }}</textarea>

                        </div>
                    @empty
                        <div class="bg-white border border-base-content/20 rounded-3xl p-8 text-center shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mx-auto text-base-content/30 mb-4"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <h3 class="text-lg font-bold text-base-content mb-1">Keranjang Belanja Kosong</h3>
                            <p class="text-sm text-base-content/60 mb-5 font-medium">Anda belum menambahkan makanan atau
                                minuman.</p>
                            <a href="{{ route('canteen.index') }}"
                                class="btn bg-fern-700 hover:bg-fern-800 text-white border-none px-6 rounded-xl font-bold text-sm shadow-md active:scale-95 transition-all">
                                Mulai Cari Menu
                            </a>
                        </div>
                    @endforelse
                </div>

                @if (count($grouped) > 0)
                    <div class="w-full lg:w-80 xl:w-96 shrink-0">
                        <x-user.cart-summary :canteens="$grouped" :total="$total" isSubmit="true" />
                    </div>
                @endif
            </div>
        </section>

    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const cartContainer = document.getElementById('cart-container');
                if (!cartContainer) return;

                // Helper untuk me-refresh DOM keranjang dan navbar secara asinkron
                async function refreshCartDOM() {
                    try {
                        const pageRes = await fetch(window.location.href, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const html = await pageRes.text();

                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const newCartContainer = doc.getElementById('cart-container');
                        if (newCartContainer) {
                            cartContainer.innerHTML = newCartContainer.innerHTML;
                        }

                        // Swap tombol keranjang di navbar agar badge quantity ter-update
                        const navbarCartBtn = document.getElementById('navbar-cart-btn');
                        const newNavbarCartBtn = doc.getElementById('navbar-cart-btn');
                        if (navbarCartBtn && newNavbarCartBtn) {
                            navbarCartBtn.innerHTML = newNavbarCartBtn.innerHTML;
                        }
                    } catch (err) {
                        console.error('Gagal memperbarui tampilan keranjang:', err);
                    }
                }



                // Delegate submit event untuk all forms inside cart-container (misal tombol Hapus)
                cartContainer.addEventListener('submit', async (e) => {
                    const form = e.target;

                    // Hanya cegah form delete (DELETE) dari cart-item
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput || methodInput.value !== 'DELETE') {
                        return;
                    }

                    e.preventDefault();

                    const submitBtn = e.submitter || form.querySelector('button[type="submit"]');
                    let originalContent = '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        originalContent = submitBtn.innerHTML;

                        // Cek jika tombolnya bulat / icon button (lebar sama dengan tinggi dan kecil)
                        const isCircleBtn = submitBtn.classList.contains('btn-circle') ||
                            submitBtn.classList.contains('rounded-full') ||
                            (submitBtn.offsetWidth > 0 && submitBtn.offsetWidth === submitBtn
                                .offsetHeight && submitBtn.offsetWidth < 50);

                        submitBtn.innerHTML = '';
                        const spinner = document.createElement('span');
                        spinner.className = isCircleBtn ?
                            'loading loading-spinner loading-xs text-base-content' :
                            'loading loading-bars loading-xs';
                        submitBtn.appendChild(spinner);
                    }

                    try {
                        const action = form.action;
                        const formData = new FormData(form);

                        const response = await fetch(action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            await refreshCartDOM();
                        } else {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalContent;
                            }
                        }
                    } catch (err) {
                        console.error(err);
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalContent;
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
