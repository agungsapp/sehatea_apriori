<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;

use function Livewire\Volt\form;
use function Livewire\Volt\layout;

layout('layouts.guest');

form(LoginForm::class);

$login = function () {
    $this->validate();

    $this->form->authenticate();

    Session::regenerate();

    $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
};

?>

<div>
		<!-- Session Status -->
		<x-auth-session-status class="mb-4" :status="session('status')" />


		<!-- Section -->
		<section class="vh-lg-100 mt-lg-0 bg-soft d-flex align-items-center mt-5">
				<div class="container">

						<div class="row justify-content-center form-bg-image"
								data-background-lg="{{ asset('volt') }}/assets/img/illustrations/signin.svg">
								<div class="col-12 d-flex align-items-center justify-content-center">
										<div class="border-light p-lg-5 w-100 fmxw-500 rounded border-0 bg-white p-4 shadow">
												<div class="text-md-center mt-md-0 mb-4 text-center">
														<h1 class="h3 mb-0">Silahkan login untuk melanjutkan</h1>
														@if (Auth::check())
																<p>logined</p>
														@endif
												</div>
												<form wire:submit="login" class="mt-4">
														<!-- Form -->
														<div class="form-group mb-4">
																<label for="email">Email</label>
																<div class="input-group">
																		<span class="input-group-text" id="basic-addon1">
																				<svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20"
																						xmlns="http://www.w3.org/2000/svg">
																						<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
																						<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
																				</svg>
																		</span>
																		<input type="email" wire:model="form.email" class="form-control" placeholder="contoh@sampel.com"
																				id="email" autofocus required>
																</div>
														</div>
														<!-- End of Form -->
														<div class="form-group">
																<!-- Form -->
																<div class="form-group mb-4">
																		<label for="password">Password</label>
																		<div class="input-group">
																				<span class="input-group-text" id="basic-addon2">
																						<svg class="icon icon-xs text-gray-600" fill="currentColor" viewBox="0 0 20 20"
																								xmlns="http://www.w3.org/2000/svg">
																								<path fill-rule="evenodd"
																										d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
																										clip-rule="evenodd"></path>
																						</svg>
																				</span>
																				<input type="password" wire:model="form.password" placeholder="Password" class="form-control"
																						id="password" required>
																		</div>
																</div>
																<!-- End of Form -->

														</div>
														<div class="d-grid">
																<button type="submit" class="btn btn-gray-800">Login</button>
														</div>
												</form>
										</div>
								</div>
						</div>
				</div>

		</section>
</div>
