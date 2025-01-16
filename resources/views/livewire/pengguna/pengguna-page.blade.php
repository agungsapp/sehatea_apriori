<div class="row gap-3">



		<div class="row">
				<h1>Data Pengguna</h1>
		</div>


		<div class="row">
				<div class="card">
						<div class="card-header">
								<div class="card-title">Tambah pengguna baru</div>
						</div>
						<form wire:submit.prevent="save">
								<div class="card-body">
										<div class="mb-3">
												<label for="name" class="form-label">Nama</label>
												<input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
														wire:model="name" placeholder="Nama lengkap...">
												@error('name')
														<div class="invalid-feedback">{{ $message }}</div>
												@enderror
										</div>
										<div class="mb-3">
												<label for="email" class="form-label">Email</label>
												<input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
														wire:model="email" placeholder="contoh@gmail.com">
												@error('email')
														<div class="invalid-feedback">{{ $message }}</div>
												@enderror
										</div>
										<div class="mb-3">
												<label for="password" class="form-label">Password</label>
												<input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
														wire:model="password" placeholder="Minimal 6 karakter">
												@error('password')
														<div class="invalid-feedback">{{ $message }}</div>
												@enderror
										</div>

								</div>
								<div class="card-footer">

										<button type="submit" class="btn btn-primary">{{ $userId ? 'Update' : 'Simpan' }}</button>
										@if ($userId)
												<button type="button" wire:click='batalEdit' class="btn btn-danger">Batal</button>
										@endif
								</div>
						</form>
				</div>
		</div>

		<div class="row">
				<div class="card">
						<div class="card-header">
								<div class="card-title">
										datatable library
								</div>
						</div>
						<div class="card-body">
								<livewire:pengguna.pengguna-table />
						</div>
				</div>
		</div>
		<script>
				Swal.fire({
						title: 'Error!',
						text: 'Do you want to continue',
						icon: 'error',
						confirmButtonText: 'Cool'
				})
		</script>
</div>
