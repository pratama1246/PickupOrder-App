@props([
    'id',
    'title' => null,
    'subtitle' => null,
    'type' => 'default', // 'default' | 'success' | 'warning' | 'error' | 'info'
    'clickOutside' => true,
    'showClose' => true,
    'showFooter' => true,
    'modalClass' => 'max-w-md',
])

<dialog id="{{ $id }}" {{ $attributes->merge(['class' => 'modal modal-bottom sm:modal-middle']) }}>
    <div
        class="modal-box bg-base-100 rounded-t-2xl sm:rounded-2xl border border-base-content/5 p-6 shadow-xl {{ $modalClass }}">

        <!-- Tombol Tutup Pojok Atas -->
        @if ($showClose)
            <button onclick="document.getElementById('{{ $id }}').close()" type="button"
                class="btn btn-sm btn-circle btn-ghost absolute right-4 top-4 text-base-content/50 hover:text-base-content"
                aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif

        <!-- Header Content -->
        @if ($title)
            <div class="mb-3 pr-6">
                <h3 class="font-bold text-lg text-base-content">
                    {{ $title }}
                </h3>
                @if ($subtitle)
                    <p class="text-xs text-base-content/60 mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
        @endif

        <!-- Body Content -->
        <div class="text-sm text-base-content/80 leading-relaxed font-medium">
            {{ $slot }}
        </div>

        <!-- Footer / Action Area -->
        @if (isset($footer))
            <div class="modal-action mt-6 flex flex-row justify-end items-center gap-2">
                {{ $footer }}
            </div>
        @elseif($showFooter)
            <div class="modal-action mt-6 flex flex-row justify-end">
                <button type="button" onclick="document.getElementById('{{ $id }}').close()"
                    class="btn bg-base-200 hover:bg-base-300 text-base-content border-none rounded-xl px-5 text-sm font-bold transition-colors">
                    Tutup
                </button>
            </div>
        @endif
    </div>

    <!-- Backdrop Form (Klik Di Luar untuk Menutup) -->
    @if ($clickOutside)
        <form method="dialog" class="modal-backdrop">
            <button class="cursor-default outline-none">close</button>
        </form>
    @endif
</dialog>
