<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="pb-16 lg:pb-0">
    <x-navbar />
    <div class="p-4 sm:p-8 md:p-16">
        <h1 class="text-2xl sm:text-3xl font-bold">Nama Saya Pratama Putra Purwanto. Lagi <span id="typing-text"></span></h1>
        <p class="text-base sm:text-lg">Hari ini saya belajar route dan view di laravel</p>
        <p class="text-sm sm:text-md">Belajar laravel asik banget kaks</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
            <x-foodcard />
            <x-foodcard />
            <x-foodcard />
            <x-foodcard />
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <x-canteencard />
            <x-canteencard />
            <x-canteencard />
        </div>
    </div>
    
    <x-dock />
    <x-footer />
</body>
<script>
const words = ["nugas?", "praktikum?", "ngoding?", "kelas?", "begadang?"];
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
</html>
