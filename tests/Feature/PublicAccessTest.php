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
}
