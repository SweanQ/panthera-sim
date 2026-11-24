<?php

use App\Models\ApparelStockMovement;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RawMaterial;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeInventoryProduct(): Product
{
    return Product::create([
        'name' => 'Kaos Panthera',
        'material' => 'Cotton Combed 24s',
        'stock_source' => 'mandiri',
        'base_price' => 85000,
    ]);
}

it('shows a dedicated employee workspace and restricts admin pages', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($employee)
        ->get('/karyawan')
        ->assertOk()
        ->assertSee('Ruang kerja karyawan')
        ->assertSee('Employee workspace');

    $this->actingAs($employee)
        ->get('/dashboard')
        ->assertForbidden();

    $customer = User::factory()->create(['role' => 'customer']);
    $this->actingAs($customer)
        ->get('/inventaris/baju')
        ->assertForbidden();

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)
        ->get('/inventaris/baju')
        ->assertOk()
        ->assertSee('PANTHERA')
        ->assertSee('Stok baju')
        ->assertDontSee('Employee workspace');
});

it('uses black inventory tables and cards with white text for admins only', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'pegawai']);
    $adminTableClass = 'class="min-w-full text-left text-sm bg-black text-white"';
    $adminHistoryTableClass = 'class="min-w-full border-collapse text-left text-sm bg-black text-white"';

    $this->actingAs($admin)->get('/inventaris/baju')
        ->assertOk()
        ->assertSee($adminTableClass, false)
        ->assertSee('border-zinc-800 bg-black text-white', false);
    $this->actingAs($admin)->get('/inventaris/riwayat')
        ->assertOk()
        ->assertSee($adminHistoryTableClass, false)
        ->assertSee('border-b border-zinc-700', false)
        ->assertDontSee('border border-zinc-700 px-5');
    $this->actingAs($admin)->get('/inventaris/bahan')
        ->assertOk()
        ->assertSee('border-zinc-800 bg-black text-white', false);

    $this->actingAs($employee)->get('/inventaris/baju')
        ->assertOk()
        ->assertDontSee('bg-black text-white');
    $this->actingAs($employee)->get('/inventaris/riwayat')
        ->assertOk()
        ->assertDontSee('bg-black text-white')
        ->assertSee('border-b border-stone-200', false)
        ->assertDontSee('border border-stone-200 px-5');
    $this->actingAs($employee)->get('/inventaris/bahan')
        ->assertOk()
        ->assertDontSee('border-zinc-800 bg-black text-white');
});

it('renders product catalog cards black with white text for admins and employees', function () {
    makeInventoryProduct();
    $cardClass = 'rounded-2xl border border-zinc-800 bg-black p-5 text-white shadow-sm';

    $this->actingAs(User::factory()->create(['role' => 'admin']))
        ->get('/inventaris/katalog')
        ->assertOk()
        ->assertSee($cardClass, false)
        ->assertSee('mt-6 rounded-2xl border border-zinc-800 bg-black p-5 text-white shadow-sm', false);

    $this->actingAs(User::factory()->create(['role' => 'pegawai']))
        ->get('/inventaris/katalog')
        ->assertOk()
        ->assertSee($cardClass, false)
        ->assertSee('mt-6 rounded-2xl border border-zinc-800 bg-black p-5 text-white shadow-sm', false);
});

it('records apparel stock movements and prevents stock from going negative', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);
    $product = makeInventoryProduct();

    $this->actingAs($employee)->post('/inventaris/baju', [
        'product_id' => $product->id,
        'size' => 'M',
        'color' => 'Hitam',
        'sku' => 'TEE-M-BLK',
        'initial_stock' => 10,
        'low_stock_threshold' => 2,
    ])->assertRedirect();

    $variant = ProductVariant::query()->sole();
    expect($variant->stock_quantity)->toBe(10);
    $this->actingAs($employee)->get('/inventaris/baju')
        ->assertOk()
        ->assertSee($product->name)
        ->assertSee('Hitam');
    $this->actingAs($employee)->patch('/inventaris/baju/' . $variant->id . '/batas-minimum', [
        'low_stock_threshold' => 4,
    ])->assertRedirect();
    expect($variant->fresh()->low_stock_threshold)->toBe(4);

    $this->assertDatabaseHas('apparel_stock_movements', [
        'product_variant_id' => $variant->id,
        'type' => 'masuk',
        'quantity' => 10,
        'stock_before' => 0,
        'stock_after' => 10,
    ]);

    $this->actingAs($employee)->patch('/inventaris/baju/' . $variant->id . '/pergerakan', [
        'type' => 'keluar',
        'quantity' => 4,
        'reason' => 'Dipakai untuk contoh produksi',
    ])->assertRedirect();

    expect($variant->fresh()->stock_quantity)->toBe(6);
    $this->assertDatabaseHas('apparel_stock_movements', [
        'product_variant_id' => $variant->id,
        'type' => 'keluar',
        'quantity' => 4,
        'stock_before' => 10,
        'stock_after' => 6,
        'reason' => 'Dipakai untuk contoh produksi',
    ]);

    $this->actingAs($employee)->patch('/inventaris/baju/' . $variant->id . '/pergerakan', [
        'type' => 'keluar',
        'quantity' => 7,
        'reason' => 'Tidak boleh melebihi saldo',
    ])->assertSessionHasErrors('quantity');

    expect($variant->fresh()->stock_quantity)->toBe(6);
});

it('deletes stock items together with their movement history', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);
    $admin = User::factory()->create(['role' => 'admin']);
    $product = makeInventoryProduct();

    $this->actingAs($employee)->post('/inventaris/baju', [
        'product_id' => $product->id,
        'size' => 'M',
        'color' => 'Hitam',
        'sku' => '',
        'initial_stock' => 10,
        'low_stock_threshold' => 2,
    ])->assertRedirect();
    $variant = ProductVariant::query()->sole();

    $this->actingAs($employee)->patch('/inventaris/baju/' . $variant->id . '/pergerakan', [
        'type' => 'keluar',
        'quantity' => 2,
        'reason' => 'Pemakaian tes',
    ])->assertRedirect();

    $this->actingAs($employee)->get('/inventaris/baju')
        ->assertOk()
        ->assertSee('Hapus varian')
        ->assertSee('seluruh riwayat pergerakan stok varian ini');
    $this->actingAs($employee)->delete('/inventaris/baju/' . $variant->id)
        ->assertRedirect();

    $this->assertDatabaseMissing('product_variants', ['id' => $variant->id]);
    $this->assertDatabaseMissing('apparel_stock_movements', ['product_variant_id' => $variant->id]);

    $this->actingAs($employee)->post('/inventaris/bahan', [
        'name' => 'Kain Cotton Combed',
        'unit' => 'meter',
        'initial_stock' => 5,
        'minimum_stock' => 1,
    ])->assertRedirect();
    $material = RawMaterial::query()->sole();

    $this->actingAs($employee)->patch('/inventaris/bahan/' . $material->id . '/pergerakan', [
        'type' => 'masuk',
        'quantity' => 2,
        'reason' => 'Stok tambahan tes',
    ])->assertRedirect();

    $this->actingAs($admin)->get('/inventaris/bahan')
        ->assertOk()
        ->assertSee('Hapus bahan')
        ->assertSee('seluruh riwayat pergerakan stok bahan ini');
    $this->actingAs($admin)->delete('/inventaris/bahan/' . $material->id)
        ->assertRedirect();

    $this->assertDatabaseMissing('raw_materials', ['id' => $material->id]);
    $this->assertDatabaseMissing('material_stock_movements', ['raw_material_id' => $material->id]);
});

it('manages raw materials with units, minimum stock, and auditable movements', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($employee)->post('/inventaris/bahan', [
        'name' => 'Kain Cotton Combed',
        'unit' => 'meter',
        'initial_stock' => 25.5,
        'minimum_stock' => 5,
        'unit_cost' => 45000,
        'notes' => 'Warna hitam',
    ])->assertRedirect();

    $material = RawMaterial::query()->sole();
    expect((float) $material->current_stock)->toBe(25.5);
    $this->actingAs($employee)->get('/inventaris/bahan')
        ->assertOk()
        ->assertSee($material->name);
    $this->actingAs($employee)->patch('/inventaris/bahan/' . $material->id, [
        'name' => 'Kain Cotton Combed Premium',
        'unit' => 'meter',
        'minimum_stock' => 7,
        'unit_cost' => 47000,
        'notes' => 'Disimpan di rak A',
    ])->assertRedirect();
    expect($material->fresh()->name)->toBe('Kain Cotton Combed Premium')
        ->and((float) $material->fresh()->minimum_stock)->toBe(7.0);

    $this->actingAs($employee)->patch('/inventaris/bahan/' . $material->id . '/pergerakan', [
        'type' => 'keluar',
        'quantity' => 2.25,
        'reason' => 'Pemakaian sampel',
    ])->assertRedirect();

    expect((float) $material->fresh()->current_stock)->toBe(23.25);
    $this->assertDatabaseHas('material_stock_movements', [
        'raw_material_id' => $material->id,
        'type' => 'keluar',
        'quantity' => 2.25,
        'stock_before' => 25.5,
        'stock_after' => 23.25,
        'reason' => 'Pemakaian sampel',
    ]);

    $this->actingAs($employee)->get('/inventaris/riwayat')
        ->assertOk()
        ->assertSee('Kain Cotton Combed')
        ->assertSee('Pemakaian sampel')
        ->assertSee($employee->name);
});

it('lets employees manage product catalog and production status without auto-consuming stock', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($employee)->post('/inventaris/katalog', [
        'name' => 'Hoodie Panthera',
        'material' => 'Fleece',
        'stock_source' => 'mandiri',
        'base_price' => 180000,
    ])->assertRedirect();

    $product = Product::query()->sole();
    $this->actingAs($employee)->get('/inventaris/katalog')
        ->assertOk()
        ->assertSee($product->name);
    $this->actingAs($employee)->patch('/inventaris/katalog/' . $product->id, [
        'name' => 'Hoodie Panthera Premium',
        'material' => 'Fleece',
        'stock_source' => 'mandiri',
        'base_price' => 180000,
    ])->assertRedirect();
    expect($product->fresh()->name)->toBe('Hoodie Panthera Premium');
    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'size' => 'L',
        'color' => 'Abu',
        'stock_quantity' => 12,
        'low_stock_threshold' => 2,
    ]);
    $customer = Customer::create([
        'name' => 'Komunitas Panthera',
        'whatsapp_number' => '6281234567890',
    ]);
    $order = Order::create([
        'order_code' => 'PNT-EMPLOYEE-ORDER',
        'customer_id' => $customer->id,
        'product_id' => $product->id,
        'size' => 'L',
        'color' => 'Abu',
        'quantity' => 3,
        'total_price' => 540000,
        'payment_status' => 'belum_dp',
        'production_status' => 'konsultasi',
        'order_source' => 'manual_web',
    ]);

    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $order->id . '/status', [
        'production_status' => 'proses_sablon',
    ])->assertRedirect();

    expect($order->fresh()->production_status)->toBe('proses_sablon')
        ->and($variant->fresh()->stock_quantity)->toBe(12);

    $this->actingAs($employee)->get('/karyawan/pesanan')
        ->assertOk()
        ->assertSee($order->order_code)
        ->assertSee('Pesanan produksi');
});

it('allows admins and employees to upload and replace catalog photos', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $employee = User::factory()->create(['role' => 'pegawai']);

    $this->actingAs($admin)->post('/inventaris/katalog', [
        'name' => 'Hoodie Panthera',
        'material' => 'Fleece',
        'stock_source' => 'mandiri',
        'base_price' => 180000,
        'image' => UploadedFile::fake()->create('hoodie.png', 10, 'image/png'),
    ])->assertRedirect();

    $product = Product::query()->sole();
    $originalImagePath = $product->image_path;
    expect($originalImagePath)->not->toBeNull();
    Storage::disk('public')->assertExists($originalImagePath);

    $this->actingAs($admin)->get('/inventaris/katalog')
        ->assertOk()
        ->assertSee(asset('storage/' . $originalImagePath), false);

    $this->actingAs($employee)->patch('/inventaris/katalog/' . $product->id, [
        'name' => $product->name,
        'material' => $product->material,
        'stock_source' => $product->stock_source,
        'base_price' => $product->base_price,
        'image' => UploadedFile::fake()->create('hoodie-new.png', 10, 'image/png'),
    ])->assertRedirect();

    $newImagePath = $product->fresh()->image_path;
    expect($newImagePath)->not->toBe($originalImagePath);
    Storage::disk('public')->assertMissing($originalImagePath);
    Storage::disk('public')->assertExists($newImagePath);

    $this->get('/')
        ->assertOk()
        ->assertSee(asset('storage/' . $newImagePath), false);

    $this->actingAs($employee)->post('/inventaris/katalog', [
        'name' => 'Produk invalid',
        'material' => 'Fleece',
        'stock_source' => 'mandiri',
        'base_price' => 180000,
        'image' => UploadedFile::fake()->create('not-an-image.txt', 10, 'text/plain'),
    ])->assertSessionHasErrors('image');

    expect(Product::count())->toBe(1);
});
