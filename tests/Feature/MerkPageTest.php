<?php

use App\Livewire\Merk\MerkPage;
use App\Models\Merk;
use App\Models\User;
use Livewire\Livewire;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('merk page can be rendered', function () {
    $this->get('/merk')
        ->assertStatus(200)
        ->assertSeeLivewire('merk.merk-page');
});

test('can create new merk', function () {
    Livewire::test(MerkPage::class)
        ->set('nama', 'New Merk')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('alert')
        ->assertDispatched('pg:eventRefresh-merk-table-yelg0w-table');

    $this->assertDatabaseHas('merks', ['nama' => 'New Merk']);
});

test('nama is required', function () {
    Livewire::test(MerkPage::class)
        ->set('nama', '')
        ->call('save')
        ->assertHasErrors(['nama' => 'required']);
});

test('nama must not exceed 255 characters', function () {
    Livewire::test(MerkPage::class)
        ->set('nama', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['nama' => 'max']);
});

test('can load merk for editing', function () {
    $merk = Merk::factory()->create();

    Livewire::test(MerkPage::class)
        ->call('loadUserForEdit', $merk->id)
        ->assertSet('isEdit', true)
        ->assertSet('merkId', $merk->id)
        ->assertSet('nama', $merk->nama)
        ->assertDispatched('alert');
});

test('can update existing merk', function () {
    $merk = Merk::factory()->create();

    Livewire::test(MerkPage::class)
        ->set('merkId', $merk->id)
        ->set('nama', 'Updated Merk')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('alert')
        ->assertDispatched('pg:eventRefresh-merk-table-yelg0w-table');

    $this->assertDatabaseHas('merks', [
        'id' => $merk->id,
        'nama' => 'Updated Merk'
    ]);
});

test('can cancel editing', function () {
    $merk = Merk::factory()->create();

    Livewire::test(MerkPage::class)
        ->set('merkId', $merk->id)
        ->set('nama', 'Updated Merk')
        ->call('batalEdit')
        ->assertSet('isEdit', false)
        ->assertSet('nama', '')
        ->assertSet('merkId', null);
});

test('can delete merk', function () {
    $merk = Merk::factory()->create();

    Livewire::test(MerkPage::class)
        ->call('deleteMerk', $merk->id)
        ->assertDispatched('alert')
        ->call('deleteMerkConfirmed')
        ->assertDispatched('alert')
        ->assertDispatched('pg:eventRefresh-merk-table-yelg0w-table');

    $this->assertDatabaseMissing('merks', ['id' => $merk->id]);
});
