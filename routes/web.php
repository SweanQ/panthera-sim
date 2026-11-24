<?php

use App\Http\Controllers\CustomInquiryController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\EmployeePortalController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/pesan', [ShopController::class, 'createOrder'])->name('shop.orders.create');
Route::post('/pesan', [ShopController::class, 'storeOrder'])->middleware('throttle:10,1')->name('shop.orders.store');
Route::get('/pesan/konfirmasi', [ShopController::class, 'orderConfirmation'])->name('shop.orders.confirmation');
Route::post('/permintaan-custom', [ShopController::class, 'storeInquiry'])->middleware('throttle:5,1')->name('shop.inquiries.store');
Route::get('/lacak', [ShopController::class, 'trackingForm'])->name('shop.orders.track');
Route::post('/lacak', [ShopController::class, 'trackOrder'])->middleware('throttle:10,1')->name('shop.orders.track.submit');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
});

Route::middleware(['auth', 'role:pegawai'])->prefix('karyawan')->name('employee.')->group(function () {
    Route::get('/', [EmployeePortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/pesanan', [EmployeePortalController::class, 'orders'])->name('orders.index');
    Route::patch('/pesanan/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('orders.cancel');
});

Route::middleware(['auth', 'role:admin,pegawai'])->prefix('inventaris')->name('inventory.')->group(function () {
    Route::get('/baju', [InventoryController::class, 'apparel'])->name('apparel');
    Route::post('/baju', [InventoryController::class, 'storeVariant'])->name('apparel.store');
    Route::patch('/baju/{variant}/batas-minimum', [InventoryController::class, 'updateVariant'])->name('apparel.threshold');
    Route::patch('/baju/{variant}/pergerakan', [InventoryController::class, 'adjustVariant'])->name('apparel.adjust');
    Route::delete('/baju/{variant}', [InventoryController::class, 'destroyVariant'])->name('apparel.destroy');
    Route::get('/bahan', [InventoryController::class, 'materials'])->name('materials');
    Route::post('/bahan', [InventoryController::class, 'storeMaterial'])->name('materials.store');
    Route::patch('/bahan/{material}', [InventoryController::class, 'updateMaterial'])->name('materials.update');
    Route::patch('/bahan/{material}/pergerakan', [InventoryController::class, 'adjustMaterial'])->name('materials.adjust');
    Route::delete('/bahan/{material}', [InventoryController::class, 'destroyMaterial'])->name('materials.destroy');
    Route::get('/riwayat', [InventoryController::class, 'history'])->name('history');
    Route::get('/katalog', [EmployeePortalController::class, 'catalog'])->name('catalog');
    Route::post('/katalog', [EmployeePortalController::class, 'storeProduct'])->name('catalog.store');
    Route::patch('/katalog/{product}', [EmployeePortalController::class, 'updateProduct'])->name('catalog.update');
    Route::patch('/katalog/{product}/arsip', [EmployeePortalController::class, 'archiveProduct'])->name('catalog.archive');
    Route::patch('/katalog/{product}/pulihkan', [EmployeePortalController::class, 'restoreProduct'])->name('catalog.restore');
    Route::delete('/katalog/{product}', [EmployeePortalController::class, 'destroyProduct'])->name('catalog.destroy');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/akun/pesanan', [CustomerOrderController::class, 'index'])->name('customer.orders');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/inquiries', [CustomInquiryController::class, 'index'])->name('inquiries.index');
    Route::patch('/inquiries/{inquiry}/status', [CustomInquiryController::class, 'updateStatus'])->name('inquiries.updateStatus');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/revision', [OrderController::class, 'incrementRevision'])->name('orders.revision');
    Route::patch('/orders/{order}/settle', [OrderController::class, 'settlePayment'])->name('orders.settle');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/reports', [OrderController::class, 'reports'])->name('reports.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';