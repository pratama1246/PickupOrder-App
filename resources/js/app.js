import axios from 'axios';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Alpine = Alpine;
window.ApexCharts = ApexCharts;

document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});



// Interseptor submit form global untuk menonaktifkan tombol submit secara asinkron guna mencegah double-submit data.
// Ini juga mengganti isi tombol dengan indikator loading agar pengguna mendapatkan umpan balik visual instan.
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.hasAttribute('@submit.prevent')) return;

    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitBtn) {
        // Penundaan 50ms diperlukan agar event submit bawaan browser telah teregistrasi sebelum elemen dinonaktifkan.
        setTimeout(() => {
            // Menonaktifkan tombol submit utama dan menginjeksikan elemen spinner loading.
            submitBtn.disabled = true;
            if (!submitBtn.querySelector('.loading')) {
                const isCircleBtn = submitBtn.classList.contains('btn-circle') ||
                    submitBtn.classList.contains('rounded-full') ||
                    (submitBtn.offsetWidth > 0 && submitBtn.offsetWidth === submitBtn.offsetHeight && submitBtn.offsetWidth < 50);

                submitBtn.innerHTML = '';
                const spinner = document.createElement('span');
                spinner.className = isCircleBtn ? 'loading loading-spinner loading-xs text-white' : 'loading loading-bars loading-md text-white';
                submitBtn.appendChild(spinner);
            }

            // Menonaktifkan semua tombol interaktif sejenis di sekitarnya untuk mencegah aksi pembatalan atau submit ganda.
            const groupContainer = form.closest('.modal-action, dialog, .modal, .flex, .grid, .modal-box') || form.parentElement;

            if (groupContainer) {
                groupContainer.querySelectorAll('button, a.btn, input[type="button"], input[type="submit"]').forEach(otherBtn => {
                    if (otherBtn !== submitBtn) {
                        otherBtn.classList.add('btn-disabled', 'pointer-events-none', 'opacity-50');
                        if (otherBtn.tagName === 'BUTTON' || otherBtn.tagName === 'INPUT') {
                            otherBtn.disabled = true;
                        }
                    }
                });
            }
        }, 50);
    }
});

// Interseptor klik tautan aktif. Jika pengguna mengklik tautan ke halaman yang sedang aktif,
// alih-alih memuat ulang halaman secara penuh, halaman akan di-scroll dengan efek smooth ke atas (Scroll to Top).
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href) {
        try {
            const targetUrl = new URL(link.href);
            if (targetUrl.origin === window.location.origin && targetUrl.pathname === window.location.pathname) {
                if (targetUrl.search === window.location.search && targetUrl.hash === window.location.hash) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        } catch (err) {
            // Abaikan kegagalan parsing untuk URL eksternal atau format href yang tidak valid.
        }
    }
});

// Inisialisasi komponen Live Search AJAX dengan Alpine.js.
// Mengambil data secara asinkron (XMLHttpRequest) dan menyisipkan konten baru langsung ke DOM target
// untuk menghindari reload halaman secara penuh dan memberikan transisi halus.
window.initLiveSearch = function (targetSelector) {
    return {
        keyword: new URLSearchParams(window.location.search).get('search') || '',
        category: new URLSearchParams(window.location.search).get('category') || '',
        canteen: new URLSearchParams(window.location.search).get('canteen') || '',
        timeout: null,
        loading: false,
        init() {
            this.$watch('keyword', value => this.triggerSearch());
            this.$watch('category', value => this.triggerSearch());
            this.$watch('canteen', value => this.triggerSearch());
            
            // Intersepsi klik pagination link di dalam container target
            // agar transisi antar halaman tetap berjalan via AJAX (tidak reload halaman).
            const container = document.querySelector(targetSelector);
            if (container) {
                container.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (link && link.href && link.closest('.pagination, nav[role="navigation"]')) {
                        e.preventDefault();
                        this.fetchData(link.href);
                    }
                });
            }
        },
        triggerSearch() {
            clearTimeout(this.timeout);
            // Delay debounce 500ms mencegah query database berlebihan saat pengguna sedang aktif mengetik kata kunci.
            this.timeout = setTimeout(() => this.fetchData(), 500);
        },
        fetchData(url = null) {
            this.loading = true;
            
            const target = document.querySelector(targetSelector);
            if (target) {
                target.classList.add('transition-all', 'duration-200', 'ease-out');
            }

            let completed = false;
            let showSkeletonTimeout = null;

            if (target && !url) {
                // Efek redup (dimming) halaman jika proses fetch memakan waktu lebih dari 150ms.
                showSkeletonTimeout = setTimeout(() => {
                    if (!completed) {
                        target.classList.add('opacity-50', 'pointer-events-none');
                    }
                }, 150);
            }

            let fetchUrl = url;
            if (!fetchUrl) {
                const params = new URLSearchParams();
                if (this.keyword) params.append('search', this.keyword);
                if (this.category) params.append('category', this.category);
                if (this.canteen) params.append('canteen', this.canteen);
                const queryString = params.toString();
                fetchUrl = window.location.pathname + (queryString ? '?' + queryString : '');
            }
            
            // Memperbarui history URL browser tanpa reload halaman agar status pencarian dapat di-bookmark/di-share.
            if (!url) {
                window.history.pushState({}, '', fetchUrl);
            }

            fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                completed = true;
                clearTimeout(showSkeletonTimeout);

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector(targetSelector);
                if (target && newContent) {
                    // Membandingkan isi HTML terlebih dahulu untuk mencegah pemicuan ulang transisi jika kontennya sama.
                    if (target.innerHTML.trim() !== newContent.innerHTML.trim()) {
                        // Menjalankan efek fade out/in (skala 95% ke 100%) agar pergantian data terasa halus di mata pengguna.
                        target.classList.add('opacity-0', 'scale-95', 'transform');
                        setTimeout(() => {
                            target.classList.remove('opacity-50', 'pointer-events-none');
                            target.innerHTML = newContent.innerHTML;
                            target.classList.remove('opacity-0', 'scale-95');
                        }, 200);
                    } else {
                        target.classList.remove('opacity-50', 'pointer-events-none');
                    }
                }
            })
            .catch(() => {
                completed = true;
                clearTimeout(showSkeletonTimeout);
                if (target) {
                    target.classList.remove('opacity-50', 'pointer-events-none');
                }
            })
            .finally(() => {
                this.loading = false;
            });
        }
    };
};
