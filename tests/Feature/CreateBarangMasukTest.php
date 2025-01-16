<?php

use App\Livewire\BarangMasuk\CreateBarangMasuk;
use App\Models\Barang;
use App\Models\Supplier;
use App\Models\User;
use Livewire\Livewire;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Seed data for suppliers and barang
    Supplier::factory(5)->create();
    Barang::factory(10)->create();
});

test('create barang masuk page can be rendered', function () {
    $this->get(route('create-barang-masuk'))
        ->assertStatus(200)
        ->assertSeeLivewire('barang-masuk.create-barang-masuk');
});

// test('can add item to keranjang', function () {
//     $supplier = Supplier::first();
//     $barang = Barang::first();

//     Livewire::test(CreateBarangMasuk::class)
//         ->dispatch('update-selected-supplier', ['value' => $supplier->id])
//         ->dispatch('update-selected-barang', ['value' => $barang->id])
//         ->set('isBaru', false)
//         ->set('jumlah', 5)
//         ->set('hargaSatuan', 10000)
//         ->set('subtotal', 50000)
//         ->call('tambahBarang')
//         ->assertSet('totalHarga', 50000)
//         ->assertCount('keranjangBarang', 1);
// });

// test('supplier and barang are required', function () {
//     Livewire::test(CreateBarangMasuk::class)
//         ->set('jumlah', 5)
//         ->set('hargaSatuan', 10000)
//         ->call('tambahBarang')
//         ->assertHasErrors(['selectedSupplier' => 'required', 'selectedBarang' => 'required']);
// });

// test('jumlah must be greater than 0', function () {
//     Livewire::test(CreateBarangMasuk::class)
//         ->dispatch('update-selected-supplier', ['value' => Supplier::first()->id])
//         ->dispatch('update-selected-barang', ['value' => Barang::first()->id])
//         ->set('jumlah', 0)
//         ->set('hargaSatuan', 10000)
//         ->call('tambahBarang')
//         ->assertHasErrors(['jumlah' => 'min']);
// });

// test('can clear keranjang after submission', function () {
//     $supplier = Supplier::first();
//     $barang = Barang::first();

//     Livewire::test(CreateBarangMasuk::class)
//         ->dispatch('update-selected-supplier', ['value' => $supplier->id])
//         ->dispatch('update-selected-barang', ['value' => $barang->id])
//         ->set('jumlah', 5)
//         ->set('hargaSatuan', 10000)
//         ->set('subtotal', 50000)
//         ->call('tambahBarang')
//         ->call('simpanTransaksi')
//         ->assertSet('keranjangBarang', [])
//         ->assertSet('totalHarga', 0);
// });
