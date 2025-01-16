<div class="row gap-3">



  <div class="row">
    <h1>Data Cabang</h1>
  </div>


  <div class="row">
    <div class="card">
      <div class="card-header">
        <div class="card-title">Tambah cabang baru </div>
      </div>
      <form wire:submit.prevent="save">
        <div class="card-body">
          <div class="mb-3">
            <label for="nama" class="form-label wajib">Nama</label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
              wire:model="nama" placeholder="Nama cabang...">
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="telepon" class="form-label">Nomor Telepon</label>
            <input type="text" class="form-control @error('telepon') is-invalid @enderror" id="telepon"
              wire:model="telepon" placeholder="nomor telepon ...">
            @error('telepon')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control @error('alamat') is-invalid @enderror" wire:model="alamat" placeholder="Jl. Raden Intan"
              id="floatingTextarea2" style="height: 100px"></textarea>
            @error('alamat')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
        <livewire:cabang.cabang-table />
      </div>
    </div>
  </div>
</div>
