<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set standard app order hours config to allow checking out (or testing)
        config(['app.order_hours.start' => '07:30']);
        config(['app.order_hours.end' => '15:30']);
        config(['app.order_days' => '*']);
    }

    public function test_guest_can_access_public_routes()
    {
        // 1. Landing Page
        $response = $this->get('/');
        $response->assertStatus(200);

        // 2. About Us Page
        $response = $this->get('/about');
        $response->assertStatus(200);

        // Create a Canteen and a Menu for detail route tests
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'is_first_login' => false,
        ]);

        $canteen = Canteen::create([
            'user_id' => $vendor->id,
            'name' => 'Kantin Jujur',
            'description' => 'Kantin sehat dan jujur.',
            'is_open' => true,
        ]);

        $menu = Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Nasi goreng dengan telur dan ayam.',
            'price' => 15000,
            'stock' => 10,
            'is_available' => true,
            'category' => 'Makanan',
        ]);

        // 3. Browse Page
        $response = $this->get('/browse');
        $response->assertStatus(200);

        // 4. Canteen Detail Page
        $response = $this->get("/canteen/{$canteen->id}");
        $response->assertStatus(200);

        // 5. Menu Detail Page
        $response = $this->get("/canteen/{$canteen->id}/menu/{$menu->id}");
        $response->assertStatus(200);
        $response->assertSee('Masuk untuk Memesan');
    }

    public function test_guest_is_redirected_to_login_on_protected_routes()
    {
        $response = $this->get('/cart');
        $response->assertRedirect('/login');

        $response = $this->get('/checkout');
        $response->assertRedirect('/login');

        $response = $this->get('/history');
        $response->assertRedirect('/login');
    }

    public function test_logged_in_mahasiswa_can_access_student_routes()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'is_first_login' => false,
        ]);

        $response = $this->actingAs($user)->get('/cart');
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get('/history');
        $response->assertStatus(200);
    }

    public function test_vendor_and_admin_cannot_access_student_transactional_routes()
    {
        $vendor = User::factory()->create([
            'role' => 'vendor',
            'is_first_login' => false,
        ]);

        $response = $this->actingAs($vendor)->get('/cart');
        $response->assertStatus(403);

        $admin = User::factory()->create([
            'role' => 'admin',
            'is_first_login' => false,
        ]);

        $response = $this->actingAs($admin)->get('/cart');
        $response->assertStatus(403);
    }

    public function test_logout_redirects_to_home()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'is_first_login' => false,
        ]);

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
    }

    public function test_logout_preserves_cart_session()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'is_first_login' => false,
        ]);

        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'is_first_login' => false,
        ]);

        $canteen = Canteen::create([
            'user_id' => $vendorUser->id,
            'name' => 'Kantin Harmoni',
            'description' => 'Kantin deskripsi',
            'is_open' => true,
        ]);

        $menu = Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Nasi Goreng',
            'description' => 'Nasi goreng lezat',
            'price' => 12000,
            'stock' => 10,
            'is_available' => true,
            'category' => 'Makanan',
        ]);

        $cart = [
            $menu->id => [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'image' => null,
                'description' => $menu->description,
                'price' => 12000.0,
                'canteen_id' => $canteen->id,
                'canteen_name' => $canteen->name,
                'quantity' => 2,
                'stock' => $menu->stock,
                'subtotal' => 24000.0,
            ],
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
        $response->assertSessionHas('cart', $cart);
    }

    public function test_guest_cart_badge_is_hidden_even_when_cart_session_exists()
    {
        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'is_first_login' => false,
        ]);

        $canteen = Canteen::create([
            'user_id' => $vendorUser->id,
            'name' => 'Kantin Harmoni',
            'description' => 'Kantin deskripsi',
            'is_open' => true,
        ]);

        $menu = Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Nasi Goreng',
            'description' => 'Nasi goreng lezat',
            'price' => 12000,
            'stock' => 10,
            'is_available' => true,
            'category' => 'Makanan',
        ]);

        $cart = [
            $menu->id => [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'price' => 12000,
                'canteen_id' => $canteen->id,
                'canteen_name' => $canteen->name,
                'quantity' => 2,
                'subtotal' => 24000,
            ],
        ];

        $response = $this->withSession(['cart' => $cart])->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('navbar-cart-count', false);
    }

    public function test_logged_in_mahasiswa_cart_badge_is_visible_when_cart_has_items()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'is_first_login' => false,
        ]);

        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'is_first_login' => false,
        ]);

        $canteen = Canteen::create([
            'user_id' => $vendorUser->id,
            'name' => 'Kantin Harmoni',
            'description' => 'Kantin deskripsi',
            'is_open' => true,
        ]);

        $menu = Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Nasi Goreng',
            'description' => 'Nasi goreng lezat',
            'price' => 12000,
            'stock' => 10,
            'is_available' => true,
            'category' => 'Makanan',
        ]);

        $cart = [
            $menu->id => [
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'price' => 12000,
                'canteen_id' => $canteen->id,
                'canteen_name' => $canteen->name,
                'quantity' => 2,
                'subtotal' => 24000,
            ],
        ];

        $response = $this->actingAs($user)
            ->withSession(['cart' => $cart])
            ->get('/');

        $response->assertStatus(200);
        $response->assertSee('navbar-cart-count', false);
    }

    public function test_cart_persists_to_database_and_syncs_across_devices()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'is_first_login' => false,
        ]);

        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'is_first_login' => false,
        ]);

        $canteen = Canteen::create([
            'user_id' => $vendorUser->id,
            'name' => 'Kantin Harmoni',
            'description' => 'Kantin deskripsi',
            'is_open' => true,
        ]);

        $menu = Menu::create([
            'canteen_id' => $canteen->id,
            'name' => 'Nasi Goreng',
            'description' => 'Nasi goreng lezat',
            'price' => 12000,
            'stock' => 10,
            'is_available' => true,
            'category' => 'Makanan',
        ]);

        // 1. Add item to cart via AJAX post
        $response = $this->actingAs($user)
            ->post(route('cart.store'), [
                'menu_id' => $menu->id,
                'quantity' => 2,
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // 2. Verify that it was saved to the database cart_items table
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        // 3. Log in again as a "different session" on a different device
        // Since we are acting as user, the next request will load it from the database automatically
        $response = $this->actingAs($user)->get('/');
        $response->assertSessionHas('cart');
        $cart = session('cart');
        $this->assertArrayHasKey($menu->id, $cart);
        $this->assertEquals(2, $cart[$menu->id]['quantity']);

        // 4. Update the cart item quantity
        $response = $this->actingAs($user)
            ->put(route('cart.update', $menu->id), [
                'quantity' => 5,
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $response->assertStatus(200);

        // Verify database is updated
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'menu_id' => $menu->id,
            'quantity' => 5,
        ]);

        // 5. Remove the item from the cart
        $response = $this->actingAs($user)
            ->delete(route('cart.destroy', $menu->id), [], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);
        $response->assertStatus(200);

        // Verify database item is deleted
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'menu_id' => $menu->id,
        ]);
    }
}
