<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

function makeOrderForCancellation(string $status = 'konsultasi'): Order
{
    $customer = Customer::create([
        'name' => 'Nadia Community',
        'whatsapp_number' => '6281234567890',
    ]);
    $product = Product::create([
        'name' => 'Kaos Panthera',
        'material' => 'Cotton Combed',
        'stock_source' => 'mandiri',
        'base_price' => 85000,
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
        'production_status' => $status,
        'order_source' => 'manual_web',
    ]);
}

function addPaymentToOrder(Order $order): Payment
{
    return Payment::create([
        'order_id' => $order->id,
        'payment_type' => 'dp',
        'amount' => 425000,
        'payment_method' => 'transfer',
        'payment_date' => now()->toDateString(),
    ]);
}

it('lets employees cancel before production with a reason and preserves payment records', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);
    $order = makeOrderForCancellation('desain_disetujui');
    $payment = addPaymentToOrder($order);

    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $order->id . '/batal', [
        'reason' => 'Pelanggan meminta perubahan jadwal.',
    ])->assertRedirect();

    $order->refresh();
    expect($order->production_status)->toBe('dibatalkan')
        ->and($order->cancelled_by)->toBe($employee->id)
        ->and($order->cancellation_reason)->toBe('Pelanggan meminta perubahan jadwal.')
        ->and($order->cancelled_at)->not->toBeNull();
    $this->assertDatabaseHas('payments', ['id' => $payment->id, 'order_id' => $order->id]);
    $this->post('/lacak', [
        'order_code' => $order->order_code,
        'whatsapp_number' => '6281234567890',
    ])->assertOk()
        ->assertSee('Pesanan dibatalkan')
        ->assertSee('tidak otomatis dikembalikan');

    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $order->id . '/status', [
        'production_status' => 'proses_sablon',
    ])->assertSessionHasErrors('order');

    $admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($admin)->patch('/orders/' . $order->id . '/settle')
        ->assertSessionHasErrors('order');

    $this->actingAs($employee)->get('/karyawan/pesanan')
        ->assertOk()
        ->assertSee('DIBATALKAN')
        ->assertSee('Pelanggan meminta perubahan jadwal.');
});

it('requires a cancellation reason and rejects cancellation after production starts', function () {
    $employee = User::factory()->create(['role' => 'pegawai']);
    $earlyOrder = makeOrderForCancellation();

    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $earlyOrder->id . '/batal', [])
        ->assertSessionHasErrors('reason');

    $productionOrder = makeOrderForCancellation('proses_sablon');
    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $productionOrder->id . '/batal', [
        'reason' => 'Terlambat untuk dibatalkan.',
    ])->assertSessionHasErrors('cancellation');

    $regressedOrder = makeOrderForCancellation();
    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $regressedOrder->id . '/status', [
        'production_status' => 'proses_sablon',
    ])->assertRedirect();
    expect($regressedOrder->fresh()->production_started_at)->not->toBeNull();

    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $regressedOrder->id . '/status', [
        'production_status' => 'desain_disetujui',
    ])->assertRedirect();
    $this->actingAs($employee)->patch('/karyawan/pesanan/' . $regressedOrder->id . '/batal', [
        'reason' => 'Produksi sudah pernah dimulai.',
    ])->assertSessionHasErrors('cancellation');

    expect($productionOrder->fresh()->production_status)->toBe('proses_sablon')
        ->and($earlyOrder->fresh()->production_status)->toBe('konsultasi')
        ->and($regressedOrder->fresh()->production_status)->toBe('desain_disetujui');
});

it('allows admins to permanently delete only after confirming the order code', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $order = makeOrderForCancellation();
    $payment = addPaymentToOrder($order);

    $this->actingAs($admin)->delete('/orders/' . $order->id, [
        'confirmation' => 'WRONG-CODE',
    ])->assertSessionHasErrors('confirmation');

    $this->assertDatabaseHas('orders', ['id' => $order->id]);
    $this->actingAs($admin)->delete('/orders/' . $order->id, [
        'confirmation' => $order->order_code,
    ])->assertRedirect(route('orders.index'));

    $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
});

it('shows admin cancellation actions and denies permanent deletion to employees', function () {
    $order = makeOrderForCancellation();
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get('/dashboard');
    $response->assertOk()
        ->assertSee('Batalkan')
        ->assertSee('Hapus permanen')
        ->assertSee('Ketik kode pesanan untuk mengonfirmasi.')
        ->assertSee('aria-modal="true"', false)
        ->assertSee($order->order_code);
    expect(strpos($response->getContent(), 'name="confirmation"'))
        ->toBeLessThan(strpos($response->getContent(), 'Ketik kode pesanan untuk mengonfirmasi.'));

    $employee = User::factory()->create(['role' => 'pegawai']);
    $this->actingAs($employee)->delete('/orders/' . $order->id, [
        'confirmation' => $order->order_code,
    ])->assertForbidden();

    $this->assertDatabaseHas('orders', ['id' => $order->id]);
});
