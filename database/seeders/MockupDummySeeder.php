<?php

namespace Database\Seeders;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MockupDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch existing canteens and their menus
        $canteenHarmoni = Canteen::where('name', 'Kantin Harmoni')->first();
        $canteenMiAcademy = Canteen::where('name', 'Mi Academy')->first();

        if (!$canteenHarmoni || !$canteenMiAcademy) {
            $this->command->error('Canteen "Kantin Harmoni" or "Mi Academy" not found. Please run DatabaseSeeder first.');
            return;
        }

        $menusHarmoni = Menu::where('canteen_id', $canteenHarmoni->id)->get();
        $menusMiAcademy = Menu::where('canteen_id', $canteenMiAcademy->id)->get();

        if ($menusHarmoni->isEmpty() || $menusMiAcademy->isEmpty()) {
            $this->command->error('Menus for canteens are empty. Please seed menus first.');
            return;
        }

        // 30 Dummy Users Data
        $usersData = [
            ['name' => 'Aditya Pratama', 'nim' => '240202201', 'email' => 'aditya.pratama@student.pnc.ac.id'],
            ['name' => 'Anisa Rahmawati', 'nim' => '240202202', 'email' => 'anisa.rahmawati@student.pnc.ac.id'],
            ['name' => 'Budi Santoso', 'nim' => '240202203', 'email' => 'budi.santoso@student.pnc.ac.id'],
            ['name' => 'Citra Lestari', 'nim' => '240202204', 'email' => 'citra.lestari@student.pnc.ac.id'],
            ['name' => 'Dwi Cahyo', 'nim' => '240202205', 'email' => 'dwi.cahyo@student.pnc.ac.id'],
            ['name' => 'Eka Putri', 'nim' => '240202206', 'email' => 'eka.putri@student.pnc.ac.id'],
            ['name' => 'Fitriani', 'nim' => '240202207', 'email' => 'fitriani@student.pnc.ac.id'],
            ['name' => 'Guntur Wibowo', 'nim' => '240202208', 'email' => 'guntur.wibowo@student.pnc.ac.id'],
            ['name' => 'Hesti Lestari', 'nim' => '240202209', 'email' => 'hesti.lestari@student.pnc.ac.id'],
            ['name' => 'Indra Wijaya', 'nim' => '240202210', 'email' => 'indra.wijaya@student.pnc.ac.id'],
            ['name' => 'Kartika Sari', 'nim' => '240202211', 'email' => 'kartika.sari@student.pnc.ac.id'],
            ['name' => 'Lukman Hakim', 'nim' => '240202212', 'email' => 'lukman.hakim@student.pnc.ac.id'],
            ['name' => 'Mega Utami', 'nim' => '240202213', 'email' => 'mega.utami@student.pnc.ac.id'],
            ['name' => 'Nurul Hidayah', 'nim' => '240202214', 'email' => 'nurul.hidayah@student.pnc.ac.id'],
            ['name' => 'Rian Hidayat', 'nim' => '240202215', 'email' => 'rian.hidayat@student.pnc.ac.id'],
            ['name' => 'Siska Amelia', 'nim' => '240202216', 'email' => 'siska.amelia@student.pnc.ac.id'],
            ['name' => 'Tri Wahyuni', 'nim' => '240202217', 'email' => 'tri.wahyuni@student.pnc.ac.id'],
            ['name' => 'Wahyu Prasetyo', 'nim' => '240202218', 'email' => 'wahyu.prasetyo@student.pnc.ac.id'],
            ['name' => 'Yeni Anggraini', 'nim' => '240202219', 'email' => 'yeni.anggraini@student.pnc.ac.id'],
            ['name' => 'Zaki Mubarak', 'nim' => '240202220', 'email' => 'zaki.mubarak@student.pnc.ac.id'],
            ['name' => 'Angga Saputra', 'nim' => '240202221', 'email' => 'angga.saputra@student.pnc.ac.id'],
            ['name' => 'Bella Safira', 'nim' => '240202222', 'email' => 'bella.safira@student.pnc.ac.id'],
            ['name' => 'Dian Purnamasari', 'nim' => '240202223', 'email' => 'dian.purnamasari@student.pnc.ac.id'],
            ['name' => 'Farhan Ramadhan', 'nim' => '240202224', 'email' => 'farhan.ramadhan@student.pnc.ac.id'],
            ['name' => 'Gita Savitri', 'nim' => '240202225', 'email' => 'gita.savitri@student.pnc.ac.id'],
            ['name' => 'Hendra Setiawan', 'nim' => '240202226', 'email' => 'hendra.setiawan@student.pnc.ac.id'],
            ['name' => 'Indah Permatasari', 'nim' => '240202227', 'email' => 'indah.permatasari@student.pnc.ac.id'],
            ['name' => 'Joko Susilo', 'nim' => '240202228', 'email' => 'joko.susilo@student.pnc.ac.id'],
            ['name' => 'Larasati', 'nim' => '240202229', 'email' => 'larasati@student.pnc.ac.id'],
            ['name' => 'Muhammad Fajar', 'nim' => '240202230', 'email' => 'muhammad.fajar@student.pnc.ac.id']
        ];

        // Clean up previously seeded mockup dummy users (and their cascade orders/reviews)
        $dummyNims = array_column($usersData, 'nim');
        User::whereIn('nim', $dummyNims)->delete();

        // 30 review ratings and comments (15 for 5-star, 10 for 4-star, 5 for 3-star)
        $reviewsPool = [
            // 5 stars
            ['rating' => 5, 'comment' => 'Makanannya enak banget, porsi pas banget buat makan siang!'],
            ['rating' => 5, 'comment' => 'Rekomendasi sekali, pelayanannya cepat dan makanannya masih hangat.'],
            ['rating' => 5, 'comment' => 'Nasi gorengnya mantap, bumbunya meresap dan pas di lidah.'],
            ['rating' => 5, 'comment' => 'Es teh manis selasihnya seger banget, pas banget diminum pas siang-siang.'],
            ['rating' => 5, 'comment' => 'Ayam gepreknya pedas mantap! Sambal koreknya juara.'],
            ['rating' => 5, 'comment' => 'Dimsumnya lembut, rasa udangnya kerasa banget dan gurih.'],
            ['rating' => 5, 'comment' => 'Mie iblis manis pedasnya pas, toping ayam taburnya melimpah.'],
            ['rating' => 5, 'comment' => 'Sangat memuaskan, kantinnya bersih dan pelayanannya ramah.'],
            ['rating' => 5, 'comment' => 'Penyajian cepat sekali, tidak perlu mengantri panjang di kantin.'],
            ['rating' => 5, 'comment' => 'Enak, higienis, dan ramah di kantong mahasiswa. Mantap!'],
            ['rating' => 5, 'comment' => 'Menu favorit saya ini! Rasanya tidak pernah mengecewakan.'],
            ['rating' => 5, 'comment' => 'Ayam gepreknya krispi dan sambalnya fresh banget.'],
            ['rating' => 5, 'comment' => 'Es teh selasihnya manisnya pas, seger banget habis kuliah.'],
            ['rating' => 5, 'comment' => 'Nasi goreng spesialnya porsinya banyak, kenyang banget.'],
            ['rating' => 5, 'comment' => 'Sistem pickup ini ngebantu banget, ga perlu antre lama lagi.'],

            // 4 stars
            ['rating' => 4, 'comment' => 'Makanannya enak, porsi lumayan mengenyangkan untuk harga segini.'],
            ['rating' => 4, 'comment' => 'Cukup enak, worth it banget dengan harganya yang terjangkau.'],
            ['rating' => 4, 'comment' => 'Makanan masih hangat saat diambil, rasanya pas.'],
            ['rating' => 4, 'comment' => 'Enak tapi tadi pas ambil antreannya agak ramai.'],
            ['rating' => 4, 'comment' => 'Rasanya pas, tapi mungkin porsi nasinya bisa ditambah sedikit lagi.'],
            ['rating' => 4, 'comment' => 'Ayamnya gurih dan renyah, sambalnya mantap tapi agak berminyak.'],
            ['rating' => 4, 'comment' => 'Pelayanannya bagus dan ramah, pesanan sesuai tanpa ada yang kurang.'],
            ['rating' => 4, 'comment' => 'Es teh manis selasihnya segar, manisnya pas tidak kemanisan.'],
            ['rating' => 4, 'comment' => 'Mie iblis pedasnya enak, cuma pangsitnya agak keras sedikit.'],
            ['rating' => 4, 'comment' => 'Sangat praktis pakai sistem pickup order ini, tinggal ambil.'],

            // 3 stars
            ['rating' => 3, 'comment' => 'Rasa standar kantin pada umumnya, porsi sedang.'],
            ['rating' => 3, 'comment' => 'Lumayan lah untuk makan siang cepat di sela-sela kelas.'],
            ['rating' => 3, 'comment' => 'Ayam gepreknya agak kurang pedas dibandingkan biasanya.'],
            ['rating' => 3, 'comment' => 'Porsi nasi gorengnya sedikit kurang banyak bagi saya.'],
            ['rating' => 3, 'comment' => 'Mienya agak terlalu lembek/kematangan, tapi rasanya oke.']
        ];

        shuffle($reviewsPool);

        // 1. Create Users
        $createdUsers = [];
        foreach ($usersData as $data) {
            $createdUsers[] = User::forceCreate([
                'name' => $data['name'],
                'nim' => $data['nim'],
                'email' => $data['email'],
                'password' => Hash::make('pncpickup123'),
                'role' => 'mahasiswa',
                'is_first_login' => false,
                'password_changed' => true,
            ]);
        }

        // 2. Create 30 Historical Completed Orders and Reviews (one for each user)
        foreach ($createdUsers as $index => $user) {
            $isHarmoni = ($index % 2 === 0);
            $selectedCanteen = $isHarmoni ? $canteenHarmoni : $canteenMiAcademy;
            $availableMenus = $isHarmoni ? $menusHarmoni : $menusMiAcademy;

            // Random dates within last 15 days (excluding today to separate today's data)
            $daysAgo = rand(1, 15);
            $randomDate = Carbon::now()->subDays($daysAgo)->subHours(rand(1, 12))->subMinutes(rand(1, 59));

            // Generate order code
            do {
                $code = 'PNC-ORD-' . $randomDate->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (Order::where('order_code', $code)->exists());

            // Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'canteen_id' => $selectedCanteen->id,
                'order_code' => $code,
                'status' => 'selesai',
                'pickup_time' => $randomDate->copy()->addMinutes(rand(15, 45)),
                'total_price' => 0,
                'notes' => 'Pesanan mockup ' . ($index + 1),
                'payment_method' => rand(0, 1) ? 'midtrans' : 'cash',
                'payment_status' => 'paid',
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);

            // Create Order Items (1 or 2 random menus)
            $numItems = rand(1, 2);
            if ($numItems === 1) {
                $selectedMenus = collect([$availableMenus->random()]);
            } else {
                $selectedMenus = $availableMenus->random(min(2, $availableMenus->count()));
            }

            $totalPrice = 0;
            foreach ($selectedMenus as $menu) {
                $qty = rand(1, 2);
                $price = $menu->price;
                $totalPrice += ($price * $qty);

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'qty' => $qty,
                    'price' => $price,
                    'notes' => rand(0, 1) ? 'Pedas sedang' : null,
                ]);
            }

            // Update Order Total Price
            $order->timestamps = false;
            $order->update(['total_price' => $totalPrice]);

            // Create Review
            $reviewData = $reviewsPool[$index];
            Review::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'menu_id' => $selectedMenus->first()->id,
                'rating' => $reviewData['rating'],
                'comment' => $reviewData['comment'],
                'is_anonymous' => (bool)rand(0, 1),
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }

        // 3. Create 5 Active/Today's Orders with diverse statuses to test dashboard transitions
        $todayStatuses = [
            [
                'status' => 'menunggu',
                'payment_status' => 'pending',
                'payment_method' => 'midtrans',
                'notes' => 'Tolong disiapkan sendok ya bu.',
                'minutes_diff' => 30, // pickup in 30 mins
            ],
            [
                'status' => 'dimasak',
                'payment_status' => 'paid',
                'payment_method' => 'midtrans',
                'notes' => 'Pedas sedang ya.',
                'minutes_diff' => 15, // pickup in 15 mins
            ],
            [
                'status' => 'siap_diambil',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'notes' => 'Saya ambil jam istirahat siang.',
                'minutes_diff' => -5, // pickup 5 mins ago
            ],
            [
                'status' => 'selesai',
                'payment_status' => 'paid',
                'payment_method' => 'cash',
                'notes' => 'Makan di kantin langsung.',
                'minutes_diff' => -60, // completed 60 mins ago
            ],
            [
                'status' => 'dibatalkan',
                'payment_status' => 'pending',
                'payment_method' => 'midtrans',
                'notes' => 'Maaf batal, ada kelas pengganti mendadak.',
                'minutes_diff' => -120, // cancelled 120 mins ago
            ]
        ];

        foreach ($todayStatuses as $i => $todayData) {
            $user = $createdUsers[$i]; // Reuse some of the created users for today's orders
            
            $isHarmoni = ($i % 2 === 0);
            $selectedCanteen = $isHarmoni ? $canteenHarmoni : $canteenMiAcademy;
            $availableMenus = $isHarmoni ? $menusHarmoni : $menusMiAcademy;

            // Anchor times during the golden/operating hours (e.g. starting around 10:00 AM today)
            $orderTime = Carbon::today()->setHour(10)->setMinute(0)->addMinutes($i * 30);
            $pickupTime = $orderTime->copy()->addMinutes($todayData['minutes_diff']);

            do {
                $code = 'PNC-ORD-' . $orderTime->format('Ymd') . '-' . strtoupper(Str::random(6));
            } while (Order::where('order_code', $code)->exists());

            $order = Order::create([
                'user_id' => $user->id,
                'canteen_id' => $selectedCanteen->id,
                'order_code' => $code,
                'status' => $todayData['status'],
                'pickup_time' => $pickupTime,
                'total_price' => 0,
                'notes' => $todayData['notes'],
                'payment_method' => $todayData['payment_method'],
                'payment_status' => $todayData['payment_status'],
                'created_at' => $orderTime,
                'updated_at' => $orderTime,
            ]);

            // Add 1 random menu item for today's order
            $menu = $availableMenus->random();
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'qty' => rand(1, 2),
                'price' => $menu->price,
                'notes' => rand(0, 1) ? 'Pakai wadah mika' : null,
            ]);

            // Calculate total price
            $totalPrice = $order->items->sum(function ($item) {
                return $item->price * $item->qty;
            });
            $order->timestamps = false;
            $order->update(['total_price' => $totalPrice]);
        }

        $this->command->info("Successfully seeded 30 users, 30 historical orders, 30 reviews, and 5 active/today's orders.");
    }
}
