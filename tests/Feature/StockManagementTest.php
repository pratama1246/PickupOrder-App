<?php

namespace Tests\Feature;

use App\Models\Canteen;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private User $vendorUser;
    private Canteen $canteen;
    private Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();
        // Set standard app order hours config to allow checking out (or testing)
        config(['app.order_hours.start' => '07:30']);
        config(['app.order_hours.end' => '15:30']);
        config(['app.order_days' => '*']);
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse('2026-06-02 10:00:00'));

        // Create student
        $this->student = User::factory()->create([
            'role' => 'mahasiswa',
            'is_first_login' => false,
        ]);

        // Create vendor user
        $this->vendorUser = User::factory()->create([
            'role' => 'vendor',
            'is_first_login' => false,
        ]);

        // Create canteen owned by vendor user
        $this->canteen = Canteen::create([
            'user_id' => $this->vendorUser->id,
            'name' => 'Kantin Test',
            'description' => 'Kantin deskripsi',
            'is_open' => true,
        ]);

        // Create menu
        $this->menu = Menu::create([
            'canteen_id' => $this->canteen->id,
            'name' => 'Ayam Geprek',
            'description' => 'Ayam geprek pedas',
            'price' => 15000,
            'stock' => 1,
            'is_available' => true,
            'category' => 'Makanan',
        ]);
    }

    protected function tearDown(): void
    {
        \Carbon\Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_cash_checkout_decrements_stock()
    {
        $cart = [
            $this->menu->id => [
                'menu_id' => $this->menu->id,
                'name' => $this->menu->name,
                'image' => $this->menu->image,
                'price' => (float) $this->menu->price,
                'canteen_id' => $this->canteen->id,
                'canteen_name' => $this->canteen->name,
                'quantity' => 1,
                'subtotal' => 15000,
            ]
        ];

        $response = $this->actingAs($this->student)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'pickup_time' => 'now',
                'payment_method' => 'bayar_di_warung',
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify stock has decreased to 0
        $this->menu->refresh();
        $this->assertEquals(0, $this->menu->stock);
    }

    public function test_insufficient_stock_fails_checkout()
    {
        // Set stock to 0
        $this->menu->update(['stock' => 0]);

        $cart = [
            $this->menu->id => [
                'menu_id' => $this->menu->id,
                'name' => $this->menu->name,
                'image' => $this->menu->image,
                'price' => (float) $this->menu->price,
                'canteen_id' => $this->canteen->id,
                'canteen_name' => $this->canteen->name,
                'quantity' => 1,
                'subtotal' => 15000,
            ]
        ];

        $response = $this->actingAs($this->student)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'pickup_time' => 'now',
                'payment_method' => 'bayar_di_warung',
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        // Should return 422 because stock is 0 (isInStock is false during syncCartWithMenus)
        $response->assertStatus(422);

        // Now test if stock is 1 but we request quantity 2
        $this->menu->update(['stock' => 1]);
        $cart[$this->menu->id]['quantity'] = 2;

        $response = $this->actingAs($this->student)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'pickup_time' => 'now',
                'payment_method' => 'bayar_di_warung',
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['success' => false]);
        $this->assertStringContainsString("tidak mencukupi", $response->json('message'));

        // Verify stock remains 1
        $this->menu->refresh();
        $this->assertEquals(1, $this->menu->stock);
    }

    public function test_user_cancellation_restores_stock()
    {
        // First checkout
        $cart = [
            $this->menu->id => [
                'menu_id' => $this->menu->id,
                'name' => $this->menu->name,
                'image' => $this->menu->image,
                'price' => (float) $this->menu->price,
                'canteen_id' => $this->canteen->id,
                'canteen_name' => $this->canteen->name,
                'quantity' => 1,
                'subtotal' => 15000,
            ]
        ];

        $this->actingAs($this->student)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'pickup_time' => 'now',
                'payment_method' => 'bayar_di_warung',
            ]);

        $this->menu->refresh();
        $this->assertEquals(0, $this->menu->stock);

        $order = Order::where('user_id', $this->student->id)->first();
        $this->assertNotNull($order);

        // Cancel order
        $response = $this->actingAs($this->student)
            ->delete(route('order.destroy', $order->id));

        $response->assertRedirect(route('order.index'));

        // Verify stock is restored to 1
        $this->menu->refresh();
        $this->assertEquals(1, $this->menu->stock);
    }

    public function test_vendor_cancellation_restores_stock()
    {
        // First checkout
        $cart = [
            $this->menu->id => [
                'menu_id' => $this->menu->id,
                'name' => $this->menu->name,
                'image' => $this->menu->image,
                'price' => (float) $this->menu->price,
                'canteen_id' => $this->canteen->id,
                'canteen_name' => $this->canteen->name,
                'quantity' => 1,
                'subtotal' => 15000,
            ]
        ];

        $this->actingAs($this->student)
            ->withSession(['cart' => $cart])
            ->post(route('checkout.store'), [
                'pickup_time' => 'now',
                'payment_method' => 'bayar_di_warung',
            ]);

        $this->menu->refresh();
        $this->assertEquals(0, $this->menu->stock);

        $order = Order::where('user_id', $this->student->id)->first();
        $this->assertNotNull($order);

        // Vendor cancel order
        $response = $this->actingAs($this->vendorUser)
            ->delete(route('vendor.order.destroy', $order->id));

        $response->assertRedirect();

        // Verify stock is restored to 1
        $this->menu->refresh();
        $this->assertEquals(1, $this->menu->stock);
    }

    public function test_midtrans_webhook_failure_restores_stock()
    {
        // Mock server key config
        config(['services.midtrans.server_key' => 'test_server_key']);

        // Set up manual order
        $order = Order::create([
            'user_id' => $this->student->id,
            'canteen_id' => $this->canteen->id,
            'order_code' => 'ORD-123456',
            'status' => 'menunggu',
            'pickup_time' => now()->addMinutes(15),
            'total_price' => 15000,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
            'payment_code' => 'PAY-123456',
        ]);

        $order->items()->create([
            'menu_id' => $this->menu->id,
            'qty' => 1,
            'price' => 15000,
        ]);

        // Manually decrement stock to simulate checkout decrement
        $this->menu->decrement('stock', 1);
        $this->assertEquals(0, $this->menu->stock);

        // Generate signature key
        $signature = hash('sha512', 'PAY-123456' . '200' . '15000' . 'test_server_key');

        // Post to webhook
        $response = $this->postJson('/payment/notification', [
            'order_id' => 'PAY-123456',
            'transaction_status' => 'failure',
            'status_code' => '200',
            'gross_amount' => '15000',
            'signature_key' => $signature,
        ]);

        $response->assertStatus(200);

        // Verify stock is restored to 1
        $this->menu->refresh();
        $this->assertEquals(1, $this->menu->stock);

        $order->refresh();
        $this->assertEquals('dibatalkan', $order->status);
        $this->assertEquals('failed', $order->payment_status);
    }

    public function test_cart_sync_returns_stock()
    {
        $cart = [
            $this->menu->id => [
                'menu_id' => $this->menu->id,
                'quantity' => 1,
            ]
        ];

        $response = $this->actingAs($this->student)
            ->withSession(['cart' => $cart])
            ->get(route('cart.index'));

        $response->assertStatus(200);
        $syncCart = session('cart');
        $this->assertEquals($this->menu->stock, $syncCart[$this->menu->id]['stock']);
    }

    public function test_checkout_automatically_excludes_out_of_stock_items()
    {
        // Set stock to 0
        $this->menu->update(['stock' => 0]);

        $cart = [
            $this->menu->id => [
                'menu_id' => $this->menu->id,
                'name' => $this->menu->name,
                'price' => 15000,
                'canteen_id' => $this->canteen->id,
                'canteen_name' => $this->canteen->name,
                'quantity' => 1,
                'subtotal' => 15000,
            ]
        ];

        // Should redirect back to cart index because the cart becomes empty after excluding out-of-stock items
        $response = $this->actingAs($this->student)
            ->withSession(['cart' => $cart])
            ->get(route('checkout.index'));

        $response->assertRedirect(route('cart.index'));
        $this->assertEquals([], session('cart'));
    }
}
