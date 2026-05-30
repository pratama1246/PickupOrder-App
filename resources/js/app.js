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

// Cropper.js instance variable
let cropperInstance = null;

function handleAvatarSelect(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        alert('Format berkas harus berupa gambar!');
        return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
        openCropperModal(e.target.result);
    };
    reader.readAsDataURL(file);
}

function openCropperModal(imageSrc) {
    const imageEl = document.getElementById('cropper_image');
    if (!imageEl) return;

    imageEl.src = imageSrc;

    const modal = document.getElementById('cropper_modal');
    if (modal) {
        modal.showModal();
    }

    // Bersihkan instance lama jika ada
    if (cropperInstance) {
        cropperInstance.destroy();
    }

    // Jalankan Cropper.js
    cropperInstance = new Cropper(imageEl, {
        aspectRatio: 1,
        viewMode: 1,
        dragMode: 'move', // Mengizinkan geser gambar
        autoCropArea: 0.8,
        restore: false,
        guides: false,
        center: false,
        highlight: false,
        cropBoxMovable: false, // Box crop tetap diam di tengah
        cropBoxResizable: false, // Tidak bisa di-resize manual box-nya (pakai zoom gambar)
        toggleDragModeOnDblclick: false,
        background: false
    });
}

function closeCropperModal(clearInput = true) {
    const modal = document.getElementById('cropper_modal');
    if (modal) {
        modal.close();
    }
    if (cropperInstance) {
        cropperInstance.destroy();
        cropperInstance = null;
    }
    if (clearInput) {
        const avatarInput = document.getElementById('avatar-input');
        if (avatarInput) {
            avatarInput.value = '';
        }
    }
}

function applyCrop() {
    if (!cropperInstance) return;

    // Dapatkan gambar hasil cropping dengan resolusi optimal 400x400
    const canvas = cropperInstance.getCroppedCanvas({
        width: 400,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    });

    canvas.toBlob(function (blob) {
        if (!blob) return;

        // Bungkus blob menjadi objek File
        const croppedFile = new File([blob], 'avatar_cropped.jpg', { type: 'image/jpeg' });

        // Masukkan file baru ke dalam input input-file bawaan
        const avatarInput = document.getElementById('avatar-input');
        if (avatarInput) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(croppedFile);
            avatarInput.files = dataTransfer.files;
        }

        // Trigger Alpine.js untuk memperbarui preview di UI
        const previewUrl = URL.createObjectURL(blob);
        window.dispatchEvent(new CustomEvent('avatar-cropped', { detail: previewUrl }));

        // Tutup modal tanpa me-reset input file yang baru kita masukkan
        closeCropperModal(false);
    }, 'image/jpeg', 0.9);
}

// Bind to window for global access (Vite bundles scripts in module scope)
window.handleAvatarSelect = handleAvatarSelect;
window.openCropperModal = openCropperModal;
window.closeCropperModal = closeCropperModal;
window.applyCrop = applyCrop;

// Type Writer Effect for Hero section
function initTypewriter() {
    const typingTextEl = document.getElementById("typing-text");
    if (!typingTextEl) return;

    const words = [
        "Nugas?",
        "Praktikum?",
        "Bikin Laporan?",
        "Ngoding?",
        "Benerin Bug?",
        "Ngebubut?",
        "Nyolder?",
        "Bikin Robot?",
        "Ngelistrik?",
        "Titrasi?",
        "Sampling Air?",
        "Olah Pangan?",
        "Pentesting?",
        "Ngedit Video?",
        "Bikin Animasi?",
        "Input Jurnal?",
        "Revisi TA?",
        "Rapat?",
        "Bikin Proposal?",
        "Evaluasi?",
        "Begadang?",
        "Mager?"
    ];
    let i = 0;
    let j = 0;
    let currentWord = "";
    let isDeleting = false;

    function typeEffect() {
        const el = document.getElementById("typing-text");
        if (!el) return;

        currentWord = words[i];

        if (isDeleting) {
            j--;
        } else {
            j++;
        }

        el.textContent = currentWord.substring(0, j);

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
}


document.addEventListener("DOMContentLoaded", () => {
    initTypewriter();
});

// Global Vanilla JS submit interceptor untuk mencegah double click dan menampilkan loading-bars
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.hasAttribute('@submit.prevent')) return;

    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
    if (submitBtn) {
        // Tunda sebentar agar proses submit form standar tetap berjalan sebelum tombol dinonaktifkan
        setTimeout(() => {
            // 1. Nonaktifkan tombol submit utama & tampilkan loading
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

            // 2. Nonaktifkan tombol-tombol lain yang berjejer (sibling) di grup/modal yang sama
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

// Active link click interceptor untuk mencegah full reload di halaman yang sama (Scroll to top)
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href) {
        try {
            const targetUrl = new URL(link.href);
            // Cek apakah url tujuan persis sama dengan url saat ini (path dan paramnya)
            if (targetUrl.origin === window.location.origin && targetUrl.pathname === window.location.pathname) {
                if (targetUrl.search === window.location.search && targetUrl.hash === window.location.hash) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        } catch (err) {
            // Abaikan URL eksternal atau href invalid
        }
    }
});

// Helper AJAX Live Search dengan Alpine.js dan skeleton transisi DaisyUI
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

            // Handle pagination clicks within the target container
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
            // Beri jeda 500ms agar user selesai mengetik sebelum mulai fetch (debounce)
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
                // Dim the content slightly if loading takes more than 150ms
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

            // Update browser URL (tanpa reload) agar status pencarian tersimpan jika halaman di-refresh
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
                        // Check if content actually changed
                        if (target.innerHTML.trim() !== newContent.innerHTML.trim()) {
                            // Restore animation for smooth content swap
                            target.classList.add('opacity-0', 'scale-95', 'transform');
                            setTimeout(() => {
                                target.classList.remove('opacity-50', 'pointer-events-none');
                                target.innerHTML = newContent.innerHTML;
                                target.classList.remove('opacity-0', 'scale-95');
                            }, 200);
                        } else {
                            // Content is the same, just remove loading state
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
