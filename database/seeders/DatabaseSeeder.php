<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        $admin = User::create([
            'name' => 'Administrator PNC',
            'nim' => 'admin',
            'email' => 'admin@pnc.ac.id',
            'password' => Hash::make('pncpickup123'),
            'role' => 'admin',
            'is_first_login' => false,
            'password_changed' => true,
        ]);

        // 2. Seed Vendor User
        $vendor = User::create([
            'name' => 'Ibu Kantin Harmoni',
            'nim' => 'vendor',
            'email' => 'kantinharmoni@pnc.ac.id',
            'password' => Hash::make('pncpickup123'),
            'role' => 'vendor',
            'is_first_login' => false,
            'password_changed' => true,
        ]);

        // Seed Canteen for Vendor
        $canteen = Canteen::create([
            'user_id' => $vendor->id,
            'name' => 'Kantin Harmoni',
            'description' => 'Menyediakan masakan prasmanan rumahan sehat dan higienis.',
            'image' => 'assets/canteen/harmoni.jpg',
            'is_open' => true,
        ]);

        // Seed Menus for Canteen
        Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan telur mata sapi, kerupuk, dan suwiran ayam.',
            'price' => 12000.00,
            'image' => 'assets/food/nasigoreng.jpg',
            'stock' => 50,
            'is_available' => true,
        ]);

        Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Ayam Geprek Sambal Korek',
            'description' => 'Ayam goreng krispi digeprek dengan cabai rawit pedas mantap.',
            'price' => 15000.00,
            'image' => 'assets/food/ayamgeprek.jpg',
            'stock' => 30,
            'is_available' => true,
        ]);

        Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Es Teh Manis Selasih',
            'description' => 'Minuman segar teh manis dingin dengan tambahan biji selasih.',
            'price' => 3000.00,
            'image' => 'assets/food/esteh.jpg',
            'stock' => 100,
            'is_available' => true,
        ]);

        // 3. Seed Mahasiswa User (NIM: 240202115, pass: pncpickup123)
        User::create([
            'name' => 'Mahasiswa Test',
            'nim' => '240202115',
            'email' => 'mahasiswa@pnc.ac.id',
            'password' => Hash::make('pncpickup123'),
            'role' => 'mahasiswa',
            'is_first_login' => true, // true agar alur test "ganti password wajib di awal" bisa teruji
            'password_changed' => false,
        ]);
    }
}
