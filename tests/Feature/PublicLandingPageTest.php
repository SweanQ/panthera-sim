<?php

use App\Models\CustomInquiry;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

function makeStoreProduct(array $attributes = []): Product
{
    return Product::create(array_merge([
        'name' => 'Kaos Panthera',
        'material' => 'Cotton Combed 24s',
        'stock_source' => 'mandiri',
        'base_price' => 85000,
    ], $attributes));
}

it('shows a searchable public catalog with filters and sorting', function () {
    makeStoreProduct();
    makeStoreProduct([
        'name' => 'Jaket Panthera',
        'material' => 'Fleece',
        'stock_source' => 'jakarta',
        'base_price' => 210000,
    ]);

    $this->get('/?q=kaos&stock_source=mandiri&sort=price_asc')
        ->assertOk()
        ->assertSee('Panthera')
        ->assertSee('Masuk')
        ->assertSee('Daftar')
        ->assertDontSee('class="cw-nav-member"', false)
        ->assertDontSee('Buat pesanan')
        ->assertSee('1 produk ditemukan')
        ->assertSee('Kaos Panthera');
});

it('shows the order navigation button only for signed-in users outside the homepage', function () {
    $this->get('/lacak')
        ->assertOk()
        ->assertDontSee('Buat pesanan');

    $customer = User::factory()->create(['role' => 'customer']);
    $this->actingAs($customer)->get('/')
        ->assertOk()
        ->assertSee('class="cw-nav-member"', false)
        ->assertDontSee('Buat pesanan');

    $this->actingAs($customer)->get('/akun/pesanan')
        ->assertOk()
        ->assertSee('class="cw-nav-member"', false)
        ->assertSee('Buat pesanan');
});

it('accepts a public order and shows its tracking code', function () {
    $product = makeStoreProduct();

    $this->post('/pesan', [
        'name' => 'Nadia Community',
        'whatsapp_number' => '+62 812-3456-7890',
        'product_id' => $product->id,
        'size' => 'L',
        'color' => 'Hitam',
        'quantity' => 12,
        'notes' => 'Logo di bagian dada',
    ])->assertRedirect(route('shop.orders.confirmation'));

    $order = Order::query()->sole();

    expect($order->user_id)->toBeNull()
        ->and($order->production_status)->toBe('konsultasi')
        ->and((float) $order->total_price)->toBe(1020000.0);

    $this->get(route('shop.orders.confirmation'))
        ->assertOk()
        ->assertSee('Permintaan diterima')
        ->assertSee($product->name)
        ->assertSee($order->order_code);
    $this->assertDatabaseHas('customers', ['whatsapp_number' => '6281234567890']);
});

it('adds signed-in customer orders to that customer account history', function () {
    $user = User::factory()->create(['role' => 'customer']);
    $product = makeStoreProduct();

    $this->actingAs($user)->post('/pesan', [
        'name' => $user->name,
        'whatsapp_number' => '081234567890',
        'product_id' => $product->id,
        'size' => 'M',
        'color' => 'Hijau',
        'quantity' => 8,
    ])->assertRedirect(route('shop.orders.confirmation'));

    $customer = $user->customer()->sole();
    $order = $customer->orders()->sole();
    $otherUser = User::factory()->create(['role' => 'customer']);
    $otherCustomer = Customer::create([
        'user_id' => $otherUser->id,
        'name' => $otherUser->name,
        'whatsapp_number' => '6281111111111',
    ]);
    Order::create([
        'order_code' => 'PNT-OTHER-CUSTOMER',
        'customer_id' => $otherCustomer->id,
        'product_id' => $product->id,
        'size' => 'S',
        'color' => 'Biru',
        'quantity' => 1,
        'total_price' => 85000,
        'payment_status' => 'belum_dp',
        'production_status' => 'konsultasi',
        'order_source' => 'manual_web',
    ]);

    expect($customer->whatsapp_number)->toBe('6281234567890');

    $this->actingAs($user)
        ->get('/akun/pesanan')
        ->assertOk()
        ->assertSee($order->order_code)
        ->assertSee($product->name)
        ->assertDontSee('PNT-OTHER-CUSTOMER');

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertForbidden();
});

it('tracks an order only when the code and WhatsApp number match', function () {
    $product = makeStoreProduct();
    $customer = Customer::create([
        'name' => 'Nadia Community',
        'whatsapp_number' => '6281234567890',
    ]);
    $order = Order::create([
        'order_code' => 'PNT-TRACK-123',
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'user_id' => null,
        'size' => 'L',
        'color' => 'Hitam',
        'quantity' => 12,
        'total_price' => 1020000,
        'payment_status' => 'belum_dp',
        'production_status' => 'konsultasi',
        'order_source' => 'manual_web',
    ]);

    $this->post('/lacak', [
        'order_code' => $order->order_code,
        'whatsapp_number' => '+62 812-3456-7890',
    ])->assertOk()
        ->assertSee($order->order_code)
        ->assertSee('Permintaan diterima');

    $this->post('/lacak', [
        'order_code' => $order->order_code,
        'whatsapp_number' => '0811111111',
    ])->assertSessionHasErrors('order_code');
});

it('stores custom inquiries and lets admins manage their status', function () {
    $product = makeStoreProduct();

    $this->post('/permintaan-custom', [
        'name' => 'Nadia Community',
        'whatsapp_number' => '081234567890',
        'product_id' => $product->id,
        'quantity' => 30,
        'details' => 'Kami membutuhkan kaos komunitas dengan desain custom.',
    ])->assertRedirect();

    $inquiry = CustomInquiry::query()->sole();
    expect($inquiry->status)->toBe('baru')
        ->and($inquiry->whatsapp_number)->toBe('6281234567890');

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)
        ->get('/inquiries')
        ->assertOk()
        ->assertSee('Nadia Community')
        ->assertSee($product->name);

    $this->actingAs($admin)
        ->patch('/inquiries/' . $inquiry->id . '/status', ['status' => 'dihubungi'])
        ->assertRedirect();

    $this->assertDatabaseHas('custom_inquiries', [
        'id' => $inquiry->id,
        'status' => 'dihubungi',
    ]);
});

it('restricts inquiry management to admins', function () {
    $staff = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($staff)
        ->get('/inquiries')
        ->assertForbidden();
});

it('shows an empty state when the product catalog is empty', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Produk belum ditemukan');
});
