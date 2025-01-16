<div class="row gap-3">



  <div class="row">
    <h1>Data Kategori Barang</h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Tambah kategori baru </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">
          <div class="mb-3">
            <label for="nama" class="form-label wajib">Nama</label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
              wire:model="nama" placeholder="Nama kategori...">
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>


        </div>
        <div class="card-footer">

          <button type="submit" class="btn btn-primary">{{ $kategoriId ? 'Update' : 'Simpan' }}</button>
          @if ($kategoriId)
            <button type="button" wire:click='batalEdit' class="btn btn-danger">Batal</button>
          @endif
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="card">
      <div class="card-body">
        <livewire:kategori.kategori-table />
      </div>
    </div>
  </div>
</div>
