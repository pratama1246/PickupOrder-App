<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        // Menggunakan firstOrCreate + forceFill karena 'role' tidak ada di $fillable.
        $admin = User::firstOrCreate(
            ['nim' => 'admin'],
            [
                'name'             => 'Administrator PNC',
                'email'            => 'admin@pnc.ac.id',
                'password'         => Hash::make('pncpickup123'),
                'is_first_login'   => false,
                'password_changed' => true,
            ]
        );
        $admin->forceFill(['role' => 'admin'])->save();

        // 2. Seed Vendor User 1
        $vendor1 = User::firstOrCreate(
            ['nim' => 'vendor_harmoni'],
            [
                'name'             => 'Ibu Kantin Harmoni',
                'email'            => 'kantinharmoni@pnc.ac.id',
                'password'         => Hash::make('pncpickup123'),
                'is_first_login'   => false,
                'password_changed' => true,
            ]
        );
        $vendor1->forceFill(['role' => 'vendor'])->save();

        // Seed Canteen 1
        $canteen1 = Canteen::updateOrCreate(
            ['user_id' => $vendor1->id],
            [
                'name' => 'Kantin Harmoni',
                'description' => 'Menyediakan masakan prasmanan rumahan sehat dan higienis.',
                'image' => 'assets/canteen/harmoni.jpg',
                'is_open' => true,
            ]
        );

        // Seed Menus for Canteen 1
        $menus1 = collect([
            Menu::updateOrCreate(['canteen_id' => $canteen1->id, 'name' => 'Nasi Goreng Spesial'], [
                'category' => 'Makanan',
                'description' => 'Nasi goreng dengan telur mata sapi, kerupuk, dan suwiran ayam.',
                'price' => 11999.00,
                'image' => 'assets/food/nasigoreng.jpg',
                'stock' => 50,
                'is_available' => true,
            ]),
            Menu::updateOrCreate(['canteen_id' => $canteen1->id, 'name' => 'Ayam Geprek Sambal Korek'], [
                'category' => 'Makanan',
                'description' => 'Ayam goreng krispi digeprek dengan cabai rawit pedas mantap.',
                'price' => 15000.00,
                'image' => 'assets/food/ayamgeprek.jpg',
                'stock' => 30,
                'is_available' => true,
            ]),
            Menu::updateOrCreate(['canteen_id' => $canteen1->id, 'name' => 'Es Teh Manis Selasih'], [
                'category' => 'Minuman',
                'description' => 'Minuman segar teh manis dingin dengan tambahan biji selasih.',
                'price' => 3000.00,
                'image' => 'assets/food/esteh.jpg',
                'stock' => 100,
                'is_available' => true,
            ]),
        ]);

        // 3. Seed Vendor User 2
        $vendor2 = User::firstOrCreate(
            ['nim' => 'vendor_mi'],
            [
                'name'             => 'Vendor Mi Academy',
                'email'            => 'miacademy@pnc.ac.id',
                'password'         => Hash::make('pncpickup123'),
                'is_first_login'   => false,
                'password_changed' => true,
            ]
        );
        $vendor2->forceFill(['role' => 'vendor'])->save();

        // Seed Canteen 2
        $canteen2 = Canteen::updateOrCreate(
            ['user_id' => $vendor2->id],
            [
                'name' => 'Mi Academy',
                'description' => 'Spesialis mie pedas dan dimsum nikmat.',
                'image' => 'assets/canteen/harmoni.jpg', // Placeholder
                'is_open' => true,
            ]
        );

        // Seed Menus for Canteen 2
        $menus2 = collect([
            Menu::updateOrCreate(['canteen_id' => $canteen2->id, 'name' => 'Mie Iblis Level 3'], [
                'category' => 'Makanan',
                'description' => 'Mie pedas manis dengan toping ayam tabur dan pangsit.',
                'price' => 11000.00,
                'image' => 'assets/food/nasigoreng.jpg',
                'stock' => 40,
                'is_available' => true,
            ]),
            Menu::updateOrCreate(['canteen_id' => $canteen2->id, 'name' => 'Dimsum Udang Rambutan'], [
                'category' => 'Makanan',
                'description' => 'Dimsum gurih isi udang dibalut kulit renyah.',
                'price' => 9500.00,
                'image' => 'assets/food/ayamgeprek.jpg',
                'stock' => 20,
                'is_available' => true,
            ]),
            Menu::updateOrCreate(['canteen_id' => $canteen2->id, 'name' => 'Lemon Tea Ice'], [
                'category' => 'Minuman',
                'description' => 'Es teh lemon segar.',
                'price' => 4500.00,
                'image' => 'assets/food/esteh.jpg',
                'stock' => 50,
                'is_available' => true,
            ]),
        ]);

        // 4. Seed Mahasiswa User
        $mahasiswa = User::firstOrCreate(
            ['nim' => 'demo_student'],
            [
                'name'             => 'Demo Student',
                'email'            => 'demo.student@pnc.ac.id',
                'password'         => Hash::make('pncpickup123'),
                'is_first_login'   => true,
                'password_changed' => false,
            ]
        );
        $mahasiswa->forceFill(['role' => 'mahasiswa'])->save();

        // Generate Dummy Historical Orders for the last 7 days
        Order::where('user_id', $mahasiswa->id)->delete();

        $statuses = ['menunggu', 'dimasak', 'siap_diambil', 'selesai', 'dibatalkan'];
        $canteens = [$canteen1, $canteen2];
        $allMenus = [$canteen1->id => $menus1, $canteen2->id => $menus2];

        for ($i = 0; $i < 40; $i++) {
            $daysAgo = rand(0, 6);
            $randomDate = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 10));
            $selectedCanteen = $canteens[array_rand($canteens)];

            do {
                $code = 'PNC-ORD-'.$randomDate->format('Ymd').'-'.strtoupper(Str::random(4));
            } while (Order::where('order_code', $code)->exists());

            $statusChoice = rand(1, 100);
            if ($statusChoice <= 50) {
                $status = 'selesai';
            } elseif ($statusChoice <= 65) {
                $status = 'dibatalkan';
            } elseif ($statusChoice <= 80) {
                $status = 'menunggu';
            } elseif ($statusChoice <= 90) {
                $status = 'dimasak';
            } else {
                $status = 'siap_diambil';
            }

            $order = Order::create([
                'user_id' => $mahasiswa->id,
                'canteen_id' => $selectedCanteen->id,
                'order_code' => $code,
                'status' => $status,
                'pickup_time' => $randomDate->copy()->addMinutes(rand(15, 60)),
                'total_price' => 0,
                'notes' => 'Pesanan dummy '.$i,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            $numItems = rand(1, 3);
            $totalPrice = 0;
            $availableMenus = $allMenus[$selectedCanteen->id];

            for ($j = 0; $j < $numItems; $j++) {
                $menu = $availableMenus->random();
                $qty = rand(1, 3);
                $price = $menu->price;
                $totalPrice += ($price * $qty);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'price' => $price,
                    'notes' => rand(0, 1) ? 'Tolong pedas' : null,
                ]);
            }

            $order->timestamps = false;
            $order->update(['total_price' => $totalPrice]);
        }

        // 5. Seed Reviews for completed orders
        $completedOrders = Order::where('status', 'selesai')->with('items')->get();
        $comments = [
            5 => ['Enak banget!', 'Rekomendasi sekali!', 'Sangat lezat dan porsi pas.', 'Makanannya hangat dan enak.', 'Pelayanan cepat dan rasa mantap!'],
            4 => ['Enak, tapi porsinya agak sedikit.', 'Rasanya pas, sesuai harga.', 'Cukup enak, worth it.', 'Makanan enak dan bersih.'],
            3 => ['Biasa saja rasanya.', 'Lumayan untuk makan siang.', 'Standard kantin pada umumnya.'],
        ];

        foreach ($completedOrders as $order) {
            foreach ($order->items as $item) {
                if (rand(1, 100) <= 75) {
                    // Distribusi: 55% bintang 5, 35% bintang 4, 10% bintang 3
                    $randVal = rand(1, 100);
                    if ($randVal <= 55) {
                        $rating = 5;
                    } elseif ($randVal <= 90) {
                        $rating = 4;
                    } else {
                        $rating = 3;
                    }

                    $commentList = $comments[$rating];
                    $comment = $commentList[array_rand($commentList)];

                    Review::updateOrCreate(
                        [
                            'user_id' => $order->user_id,
                            'order_id' => $order->id,
                            'menu_id' => $item->menu_id,
                        ],
                        [
                            'rating' => $rating,
                            'comment' => $comment,
                            'is_anonymous' => (bool)rand(0, 1),
                            'created_at' => $order->created_at,
                            'updated_at' => $order->created_at,
                        ]
                    );
                }
            }
        }
    }
}
