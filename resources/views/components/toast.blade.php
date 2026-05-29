@php
    $toasts = [];
    if (session('success')) {
        $toasts[] = ['type' => 'success', 'message' => session('success')];
    }
    if (session('error')) {
        $toasts[] = ['type' => 'error', 'message' => session('error')];
    }
    if (session('error_list')) {
        $html = '<span class="font-bold block mb-1">Beberapa baris data gagal diimpor:</span>';
        $html .= '<ul class="list-disc pl-4 text-xs space-y-0.5 font-medium max-h-32 overflow-y-auto scrollbar-thin">';
        foreach (session('error_list') as $err) {
            $html .= '<li>' . e($err) . '</li>';
        }
        $html .= '</ul>';
        $toasts[] = ['type' => 'error', 'message' => $html];
    }
    if (session('warning')) {
        $toasts[] = ['type' => 'warning', 'message' => session('warning')];
    }
    if (session('info')) {
        $toasts[] = ['type' => 'info', 'message' => session('info')];
    }
    if (session('status')) {
        $status = session('status');
        if ($status === 'profile-updated') {
            $toasts[] = ['type' => 'success', 'message' => 'Informasi profil berhasil diperbarui!'];
        } elseif ($status === 'password-updated') {
            $toasts[] = ['type' => 'success', 'message' => 'Password berhasil diperbarui!'];
        } elseif ($status === 'reset-sent') {
            $toasts[] = ['type' => 'success', 'message' => 'Link reset kata sandi telah dikirim ke email Anda.'];
        } elseif ($status === 'no-email') {
            $toasts[] = [
                'type' => 'warning',
                'message' => 'Akun Anda belum memiliki alamat email terdaftar. Hubungi admin.',
            ];
        } else {
            $toasts[] = ['type' => 'info', 'message' => $status];
        }
    }
@endphp

<div x-data="{
    toasts: {{ json_encode($toasts) }},
    addToast(message, type = 'success') {
        const id = Date.now() + Math.random().toString(36).substr(2, 9);
        this.toasts.push({ id, message, type, show: false, progress: 100 });

        this.$nextTick(() => {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.show = true;
                this.startTimer(toast);
            }
        });
    },
    removeToast(id) {
        const toast = this.toasts.find(t => t.id === id);
        if (toast) {
            toast.show = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300); // Wait for transition out
        }
    },
    startTimer(toast) {
        const duration = 4000;
        const start = Date.now();

        const interval = setInterval(() => {
            const elapsed = Date.now() - start;
            toast.progress = Math.max(0, 100 - (elapsed / duration) * 100);
            if (elapsed >= duration) {
                clearInterval(interval);
                this.removeToast(toast.id);
            }
        }, 50);
    },
    init() {
        // Assign IDs to initial session toasts and start their timers
        this.toasts = this.toasts.map(t => ({
            id: Date.now() + Math.random().toString(36).substr(2, 9),
            message: t.message,
            type: t.type,
            show: false,
            progress: 100
        }));

        this.toasts.forEach(toast => {
            setTimeout(() => {
                toast.show = true;
                this.startTimer(toast);
            }, 100);
        });

        // Listen for global notification events
        window.addEventListener('notify', (e) => {
            this.addToast(e.detail.message, e.detail.type || 'success');
        });
    }
}"
    class="fixed z-[9999] flex flex-col gap-3 w-[calc(100%-2rem)] sm:w-full max-w-sm pointer-events-none bottom-24 left-1/2 -translate-x-1/2 sm:bottom-auto sm:left-auto sm:translate-x-0 sm:top-24 sm:right-4">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-[20px] sm:translate-y-[-20px] translate-x-0 sm:translate-x-[20px] opacity-0 scale-95"
            x-transition:enter-end="translate-y-0 translate-x-0 opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-y-0 translate-x-0 opacity-100 scale-100"
            x-transition:leave-end="translate-y-[50px] sm:translate-y-0 sm:translate-x-[50px] opacity-0 scale-95"
            class="pointer-events-auto shadow-md rounded-2xl p-4 flex gap-3 relative overflow-hidden transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg border"
            :class="{
                'alert alert-success bg-white border-emerald-200 text-emerald-800': toast.type === 'success',
                'alert alert-warning bg-white border-amber-200 text-amber-800': toast.type === 'warning',
                'alert alert-error bg-white border-red-200 text-red-800': toast.type === 'error',
                'alert alert-info bg-white border-blue-200 text-blue-800': toast.type === 'info'
            }"
            role="alert">
            <!-- SVG Icon -->
            <div class="shrink-0 pt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="h-6 w-6 shrink-0 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </template>
            </div>

            <!-- Toast Content -->
            <div class="flex-1 pr-4">
                <span class="text-sm font-semibold leading-relaxed" x-html="toast.message"></span>
            </div>

            <!-- Close Button -->
            <button @click="removeToast(toast.id)"
                class="absolute top-3 right-3 text-current/50 hover:text-current active:scale-95 transition-all p-0.5 rounded-full hover:bg-current/10 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Bottom Progress Line -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-current/10">
                <div class="h-full bg-current transition-all linear duration-75" :style="`width: ${toast.progress}%`">
                </div>
            </div>
        </div>
    </template>
</div>
