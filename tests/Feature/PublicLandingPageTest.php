<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the public landing page and product catalog without authentication', function () {
    Product::create([
        'name' => 'Kaos Custom Panthera',
        'material' => 'Cotton Combed 24s',
        'stock_source' => 'mandiri',
        'base_price' => 85000,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Kaos Custom Panthera')
        ->assertSee('Cotton Combed 24s')
        ->assertSee('Mulai Rp 85.000')
        ->assertSee(route('login'), false);
});

it('shows an empty state when there are no products', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Koleksi sedang disiapkan');
});
