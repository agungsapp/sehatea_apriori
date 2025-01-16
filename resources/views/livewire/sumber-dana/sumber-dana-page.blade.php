<div class="row gap-3">

  <div class="row">
    <h1 class="text-capitalize">Data {{ $context }} </h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Tambah {{ $context }} baru </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">
          <div class="row">

            <div class="col-3 mb-3">
              <label for="nama" class="form-label text-capitalize wajib">Nama</label>
              <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                wire:model="nama" placeholder="nama {{ $context }}...">
              @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>

        </div>
        <div class="card-footer">

          <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
          @if ($isEdit)
            <button type="button" wire:click='batalEdit' class="btn btn-danger">Batal</button>
          @endif
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="card">
      <div class="card-body">
        <livewire:SumberDana.sumber-dana-table />
      </div>
    </div>
  </div>
</div>
