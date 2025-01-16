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

            <div class="col-3 mb-3">
              <label for="satuan" class="form-label wajib">
                Satuan Bahan
              </label>
              <select id="satuan" class="form-control choice @error('satuan') is-invalid @enderror form-select"
                wire:model="satuan">
                @forelse ($satuans as $satuan)
                  <option value="{{ $satuan->nama }}">{{ $satuan->nama }}</option>
                @empty
                  <option>NO_DATA</option>
                @endforelse
              </select>
              @error('satuan')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-3 mb-3">
              <label for="stok" class="form-label text-capitalize wajib">stok</label>
              <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok"
                wire:model="stok" placeholder="stok {{ $context }}...">
              @error('stok')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

          </div>

          <div class="row">
            <div class="col-12">
              <label for="catatan" class="form-label">Catatan</label>
              <textarea id="catatan" wire:model="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                placeholder="catatan transaksi (opsional)"></textarea>
              @error('catatan')
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
        <livewire:bahan.bahan-table />
      </div>
    </div>
  </div>
</div>
