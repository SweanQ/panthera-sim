<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

function makeCatalogLifecycleProduct(string $name = 'Produk Arsip Panthera'): Product
{
    return Product::create([
        'name' => $name,
        'material' => 'Cotton Combed',
        'stock_source' => 'mandiri',
        'base_price' => 85000,
    ]);
}

function attachOrderToCatalogProduct(Product $product): Order
{
    $customer = Customer::create([
        'name' => 'Komunitas Panthera',
        'whatsapp_number' => '6281234567890',
    ]);

    return Order::create([
        'order_code' => 'PNT-' . strtoupper((string) \Illuminate\Support\Str::ulid()),
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'size' => 'M',
        'color' => 'Hitam',
        'quantity' => 10,
        'total_price' => 850000,
        'payment_status' => 'belum_dp',
        'production_status' => 'konsultasi',
        'order_source' => 'manual_web',
    ]);
}

it('lets employees archive and admins restore products without removing order history', function () {
    $product = makeCatalogLifecycleProduct();
    $order = attachOrderToCatalogProduct($product);
    $employee = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($employee)->patch('/inventaris/katalog/' . $product->id . '/arsip')
        ->assertRedirect();

    expect($product->fresh()->is_active)->toBeFalse();
    $this->assertDatabaseHas('orders', ['id' => $order->id, 'product_id' => $product->id]);
    $this->get('/')
        ->assertOk()
        ->assertDontSee('Produk Arsip Panthera');
    $this->get('/pesan')
        ->assertOk()
        ->assertDontSee('Produk Arsip Panthera');
    $this->post('/pesan', [
        'name' => 'Pelanggan Baru',
        'whatsapp_number' => '081234567890',
        'product_id' => $product->id,
        'size' => 'L',
        'color' => 'Putih',
        'quantity' => 1,
    ])->assertSessionHasErrors('product_id');

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->patch('/inventaris/katalog/' . $product->id . '/pulihkan')
        ->assertRedirect();

    expect($product->fresh()->is_active)->toBeTrue();
    $this->get('/')
        ->assertOk()
        ->assertSee('Produk Arsip Panthera');
});

it('allows permanent deletion only for products without orders or stock variants', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);
    $unusedProduct = makeCatalogLifecycleProduct('Produk Belum Dipakai');

    $this->actingAs($employee)->delete('/inventaris/katalog/' . $unusedProduct->id, [
        'confirmation' => 'Nama salah',
    ])->assertSessionHasErrors('confirmation');
    $this->assertDatabaseHas('products', ['id' => $unusedProduct->id]);

    $this->actingAs($employee)->delete('/inventaris/katalog/' . $unusedProduct->id, [
        'confirmation' => 'Produk Belum Dipakai',
    ])->assertRedirect(route('inventory.catalog'));
    $this->assertDatabaseMissing('products', ['id' => $unusedProduct->id]);

    $orderedProduct = makeCatalogLifecycleProduct('Produk Berpesanan');
    $order = attachOrderToCatalogProduct($orderedProduct);
    $this->actingAs($employee)->delete('/inventaris/katalog/' . $orderedProduct->id, [
        'confirmation' => 'Produk Berpesanan',
    ])->assertSessionHasErrors('product');
    $this->assertDatabaseHas('products', ['id' => $orderedProduct->id]);
    $this->assertDatabaseHas('orders', ['id' => $order->id]);

    $stockedProduct = makeCatalogLifecycleProduct('Produk Berstok');
    ProductVariant::create([
        'product_id' => $stockedProduct->id,
        'size' => 'S',
        'color' => 'Biru',
        'stock_quantity' => 0,
        'low_stock_threshold' => 0,
    ]);
    $this->actingAs($employee)->delete('/inventaris/katalog/' . $stockedProduct->id, [
        'confirmation' => 'Produk Berstok',
    ])->assertSessionHasErrors('product');
    $this->assertDatabaseHas('products', ['id' => $stockedProduct->id]);
});

it('restricts catalog removal actions to admins and employees', function () {
    $product = makeCatalogLifecycleProduct();
    $customer = User::factory()->create(['role' => 'customer']);

    $this->actingAs($customer)->patch('/inventaris/katalog/' . $product->id . '/arsip')
        ->assertForbidden();
    $this->actingAs($customer)->delete('/inventaris/katalog/' . $product->id, [
        'confirmation' => $product->name,
    ])->assertForbidden();

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->get('/inventaris/katalog')
        ->assertOk()
        ->assertSee('<body class="min-h-screen bg-white text-zinc-900', false)
        ->assertSee('Hapus permanen')
        ->assertSee('Arsipkan produk');
});
