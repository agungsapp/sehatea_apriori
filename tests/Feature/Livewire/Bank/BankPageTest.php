<?php

use App\Livewire\Bank\BankPage;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(BankPage::class)
        ->assertStatus(200);
});
