<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Middleware Keamanan HTTP & Konfigurasi Content Security Policy (CSP):
     * - Mengamankan seluruh respons HTTP aplikasi dari ancaman Clickjacking dan MIME Sniffing.
     * - Mengatur kebijakan Referrer Policy untuk melindungi kebocoran metadata lokasi navigasi pengguna.
     * - Memformulasikan Content Security Policy (CSP) secara dinamis:
     *   - Membolehkan integrasi pustaka eksternal Midtrans (sandbox dan produksi) untuk memproses pembayaran Snap.
     *   - Mendeteksi lingkungan lokal (local/development) secara otomatis untuk menyuntikkan origin Vite Dev Server
     *     (termasuk protokol WebSocket ws://) agar fitur Hot Module Replacement (HMR) tetap berfungsi tanpa terblokir CSP.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mencegah aplikasi dimuat di dalam iframe/frame situs lain untuk memitigasi serangan hijacking klik
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Memaksa peramban mematuhi tipe MIME resmi dari server guna mencegah eksekusi file terselubung
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Melindungi informasi rujukan navigasi asal saat mengakses tautan lintas-asal
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        $midtransSandbox = 'https://app.sandbox.midtrans.com';
        $midtransProd    = 'https://app.midtrans.com';

        // Menentukan asal (origin) server Vite secara otomatis di lokal agar modul HMR dapat terhubung tanpa hambatan
        $viteOrigin = '';
        if (app()->environment(['local', 'development'])) {
            $appUrl     = rtrim(config('app.url'), '/');
            $viteHost   = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost';
            $viteOrigin = "http://{$viteHost}:5173 ws://{$viteHost}:5173";
        }

        // Membangun string Content Security Policy (CSP) untuk membatasi eksekusi aset berbahaya:
        // - script-src: membolehkan skrip internal, pustaka inline (Alpine.js/Tailwind), server lokal Vite, dan pemutar Midtrans.
        // - style-src: membolehkan inline styles untuk animasi reaktif dan Google Fonts.
        // - connect-src: membolehkan AJAX request ke server lokal, WebSocket Vite, serta API gerbang pembayaran Midtrans.
        $csp = implode('; ', array_filter([
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' {$viteOrigin} {$midtransSandbox} {$midtransProd} https://cdn.jsdelivr.net https://unpkg.com https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' {$viteOrigin} https://fonts.googleapis.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: blob: https:",
            "frame-src 'self' {$midtransSandbox} {$midtransProd}",
            "connect-src 'self' {$viteOrigin} {$midtransSandbox} {$midtransProd}",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]));

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
