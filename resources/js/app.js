import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

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
    reader.onload = function(e) {
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

    canvas.toBlob(function(blob) {
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

    const words = ["Nugas?", "Praktikum?", "Ngoding?", "Kelas?", "Begadang?"];
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
