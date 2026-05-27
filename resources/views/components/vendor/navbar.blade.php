<header
    class="navbar bg-shadow-grey-900 h-20 flex items-center justify-between px-4 sm:px-6 shrink-0 z-50 shadow-md">
    <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
        <div class="bg-fern-700 text-white font-bold text-lg px-3 py-1 rounded-lg tracking-wide shrink-0">
            LOGO
        </div>

        <div x-data="{
            hover: false,
            scrollDist: 0,
            check() {
                const el = this.$refs.text;
                const isTruncated = el.classList.contains('truncate');
                if (isTruncated) el.classList.remove('truncate', 'block');
                el.classList.add('whitespace-nowrap', 'inline-block');
        
                this.scrollDist = el.scrollWidth - this.$refs.container.clientWidth;
        
                el.classList.remove('whitespace-nowrap', 'inline-block');
                if (isTruncated) el.classList.add('truncate', 'block');
            }
        }" @mouseenter="check(); hover = true" @mouseleave="hover = false" x-ref="container"
            class="min-w-0 flex-1 overflow-hidden">

            <div x-ref="text"
                class="text-white/90 font-bold text-base sm:text-lg tracking-wide transition-transform ease-linear"
                :class="hover && scrollDist > 0 ? 'whitespace-nowrap inline-block' : 'truncate block'"
                :style="hover && scrollDist > 0 ?
                    `transform: translateX(-${scrollDist + 8}px); transition-duration: ${Math.max(scrollDist * 20, 500)}ms;` :
                    'transform: translateX(0); transition-duration: 300ms;'">
                {{ optional(auth()->user()->canteen)->name ?? 'Vendor' }} <span
                    class="max-[380px]:hidden">Dashboard</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="dropdown dropdown-end ml-1">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                @if (auth()->check() && auth()->user()->avatar)
                    <div class="w-8 rounded-full ring-2 ring-fern-200">
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="avatar"
                            class="object-cover" />
                    </div>
                @else
                    <div
                        class="bg-fern-100 text-fern-700 w-8 rounded-full ring-2 ring-fern-200 flex items-center justify-center">
                        <span class="text-sm font-bold uppercase">{{ substr(auth()->user()->name ?? 'V', 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <ul tabindex="0"
                class="menu menu-md dropdown-content bg-base-100 rounded-box z-10 mt-3 w-64 p-2 shadow-lg border border-base-200 text-base-content">
                <div class="px-4 py-2.5 border-b border-base-200 mb-1 min-w-0">
                    <div class="text-[10px] font-bold text-fern-700 uppercase tracking-wider">Vendor</div>
                    <div class="text-sm font-bold text-base-content truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-base-content/50 truncate">{{ auth()->user()->email }}</div>
                </div>
                <li><a href="{{ route('home') }}" class="font-medium">Halaman Utama</a></li>
                <li><a href="{{ route('vendor.canteen.edit') }}" class="font-medium">Pengaturan Kantin</a></li>
                <li><a href="{{ route('profile.edit') }}" class="font-medium">Pengaturan Akun</a></li>
                <li class="border-t border-base-200 my-1"></li>
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-error font-medium">Keluar</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
